<?php

namespace App\Entity\Development;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\Development\DevelopmentFileRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=DevelopmentFileRepository::class)
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
    denormalizationContext: ['groups' => ['file:write'], 'enable_max_depth' => true],
    normalizationContext: ['groups' => ['file:read'], 'enable_max_depth' => true],
)]
class DevelopmentFile
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    #[Groups(['file:read','file:write','development:read'])]
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Groups(['file:read','file:write','development:read'])]
    private string $name;

    /**
     * @ORM\ManyToOne(targetEntity=Development::class, inversedBy="files")
     * @ORM\JoinColumn(onDelete="CASCADE", nullable=false)
     */
    #[Groups(['file:read'])]
    private Development $developments;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    #[Groups(['file:read','file:write','development:read'])]
    private DateTimeImmutable $createdAt;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    #[Groups(['file:read','file:write','development:read'])]
    private ?DateTimeImmutable $updatedAt = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    #[Groups(['file:read','file:write','development:read'])]
    private string $path;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
    }

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

    public function getDevelopments(): Development
    {
        return $this->developments;
    }

    public function setDevelopments(Development $developments): self
    {
        $this->developments = $developments;
        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;
        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }
}
