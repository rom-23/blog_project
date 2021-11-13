<?php

namespace App\Entity\Development;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\Development\TagRepository;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass=TagRepository::class)
 */
#[ApiResource(
    collectionOperations: [
        'get',
        'post'
    ],
    itemOperations: [
        'get',
        'patch',
        'delete',
        'put'
    ],
    denormalizationContext: ['groups' => ['tag:write'], 'enable_max_depth' => true],
    normalizationContext: ['groups' => ['tag:read'], 'enable_max_depth' => true],
)]
class Tag
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    #[Groups(['tag:read','tag:write','development:read'])]
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[Groups(['tag:read','tag:write','development:read'])]
    #[Assert\NotBlank]
    private ?string $name;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    #[Pure] public function __toString()
    {
        return $this->name ?: '';
    }

}
