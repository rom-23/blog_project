<?php

namespace App\Entity\Modelism;

use App\Entity\User;
use App\Repository\Modelism\OpinionRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=OpinionRepository::class)
 */
class Opinion
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    #[Groups(['opinion:read', 'opinion:write','user:read'])]
    private ?int $id = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    #[Groups(['opinion:read', 'opinion:write','user:read'])]
    private ?int $vote = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    #[Groups(['opinion:read', 'opinion:write','user:read'])]
    private ?string $comment = null;

    /**
     * @ORM\Column(type="date_immutable", nullable=false)
     */
    private DateTimeImmutable $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="opinions")
     * @ORM\JoinColumn(onDelete="CASCADE", nullable=false)
     */
    #[Groups(['opinion:read'])]
    private ?User $user;

    /**
     * @ORM\ManyToOne(targetEntity=Model::class, inversedBy="opinions")
     * @ORM\JoinColumn(onDelete="CASCADE", nullable=false)
     */
    #[Groups(['opinion:read', 'user:read'])]
    private ?Model $model;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVote(): ?int
    {
        return $this->vote;
    }

    public function setVote(?int $vote): self
    {
        $this->vote = $vote;
        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;
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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getModel(): ?Model
    {
        return $this->model;
    }

    public function setModel(?Model $model): self
    {
        $this->model = $model;
        return $this;
    }

    public function __toString(): string
    {
        return $this->comment ?: '';
    }
}
