<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
class Project
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    #[Groups(['project:read', 'skill:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true, type: Types::STRING)]
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Groups(['project:read', 'project:write', 'skill:read'])]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true, type: Types::STRING)]
    #[Groups(['project:read', 'project:write'])]
    #[Assert\Regex(pattern: '/^#/')]
    private ?string $color = null;

    #[ORM\Column(length: 255, nullable: true, type: Types::STRING)]
    #[Groups(['project:read', 'project:write'])]
    private ?string $subtitle = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotNull]
    #[Assert\NotBlank]
    #[Groups(['project:read', 'project:write'])]
    private ?string $description = null;

    #[ORM\Column(nullable: true, type: Types::BOOLEAN)]
    #[Groups(['project:read', 'project:write'])]
    private ?bool $isPersonal = null;

    #[ORM\Column(length: 255, nullable: true, type: Types::STRING)]
    #[Groups(['project:read', 'project:write'])]
    #[Assert\Url]
    private ?string $link = null;

    #[ORM\ManyToMany(targetEntity: Skill::class, inversedBy: 'projects')]
    #[ORM\JoinTable(name: 'projects_skills')]
    #[Groups(['project:read', 'project:write'])]
    private Collection $skills;

    public function __construct()
    {
        $this->skills = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): static
    {
        $this->color = $color;

        return $this;
    }

    public function getSubtitle(): ?string
    {
        return $this->subtitle;
    }

    public function setSubtitle(?string $subtitle): static
    {
        $this->subtitle = $subtitle;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function isIsPersonal(): ?bool
    {
        return $this->isPersonal;
    }

    public function setIsPersonal(bool $isPersonal): static
    {
        $this->isPersonal = $isPersonal;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(?string $link): static
    {
        $validator  = Validation::createValidator();
        $violations = $validator->validate($link, new Assert\Url());

        if (count($violations) > 0) {
            if (!preg_match('#^https://#', $link)) {
                $link = 'https://' . $link;
            }
        }

        $this->link = $link;

        return $this;
    }

    /**
     * @return Collection<int, Skill>
     */
    public function getSkills(): Collection
    {
        return $this->skills;
    }

    public function addSkill(Skill $skill): static
    {
        if (!$this->skills->contains($skill)) {
            $this->skills->add($skill);
        }

        return $this;
    }

    public function removeSkill(Skill $skill): static
    {
        $this->skills->removeElement($skill);

        return $this;
    }
}
