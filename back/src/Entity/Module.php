<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ModuleRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: ModuleRepository::class)]
class Module
{
    private const ALLOWED_OPTIONS_KEYS = [
        'color_title',
        'color_text',
        'color_secondary',
        'color_primary',
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    #[Groups(['module:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true, type: Types::STRING)]
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Groups(['module:read', 'module:write'])]
    private ?string $name = null;

    #[ORM\Column(length: 50, type: Types::STRING)]
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Groups(['module:read', 'module:write'])]
    private ?string $icon = null;

    #[ORM\Column(nullable: true, type: Types::JSON)]
    #[Groups(['module:read', 'module:write'])]
    private ?array $options = null;

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

    public function getOptions(): ?array
    {
        return $this->options;
    }

    public function setOptions(?array $options): static
    {
        $this->options = $options;

        return $this;
    }

    #[Assert\Callback]
    public function validateOptions(ExecutionContextInterface $context, $payload)
    {
        if ($this->options !== null) {
            foreach ($this->options as $key => $value) {
                if (!in_array($key, self::ALLOWED_OPTIONS_KEYS)) {
                    $context->buildViolation('Invalid key "{{ key }}" in options.')
                        ->setParameter('{{ key }}', $key)
                        ->atPath('options')
                        ->addViolation();
                }
                if (!is_string($value)) {
                    $context->buildViolation('The value for "{{ key }}" must be a string.')
                        ->setParameter('{{ key }}', $key)
                        ->atPath('options')
                        ->addViolation();
                }
            }
        }
    }
}
