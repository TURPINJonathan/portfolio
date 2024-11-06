<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\SkillRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SkillRepository::class)]
class Skill
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    #[Groups(['skill:read', 'project:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true, type: Types::STRING)]
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Groups(['skill:read', 'skill:write', 'project:read'])]
    private ?string $name = null;

    #[ORM\Column(length: 255, type: Types::STRING)]
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Groups(['skill:read', 'skill:write', 'project:read'])]
    private ?string $icon = null;

    #[ORM\Column(length: 255, type: Types::STRING)]
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Regex(pattern: '/^#/')]
    #[Groups(['skill:read', 'skill:write', 'project:read'])]
    private ?string $color = null;

    #[ORM\Column(nullable: true, type: Types::BOOLEAN)]
    #[Groups(['skill:read', 'skill:write', 'project:read'])]
    private ?bool $isHardSkill = null;

    #[ORM\ManyToMany(targetEntity: Project::class, mappedBy: 'skills')]
    #[Groups(['skill:read'])]
    private Collection $projects;

    public function __construct()
    {
        $this->projects = new ArrayCollection();
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

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setIcon(string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): static
    {
        $this->color = $color;

        return $this;
    }

    public function isIsHardSkill(): ?bool
    {
        return $this->isHardSkill;
    }

    public function setIsHardSkill(bool $isHardSkill): static
    {
        $this->isHardSkill = $isHardSkill;

        return $this;
    }

    /**
     * @return Collection<int, Project>
     */
    public function getProjects(): Collection
    {
        return $this->projects;
    }

    public function addProject(Project $project): static
    {
        if (!$this->projects->contains($project)) {
            $this->projects->add($project);
            $project->addSkill($this);
        }

        return $this;
    }

    public function removeProject(Project $project): static
    {
        if ($this->projects->removeElement($project)) {
            $project->removeSkill($this);
        }

        return $this;
    }
}
