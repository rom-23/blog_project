<?php

namespace App\Entity\Development;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\User;
use App\Repository\Development\PostRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass=PostRepository::class)
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
    denormalizationContext: ['groups' => ['post:write'], 'enable_max_depth' => true],
    normalizationContext: ['groups' => ['post:read'], 'enable_max_depth' => true],
)]

class Post
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    #[Groups(['post:read','post:write','user:read','development:read'])]
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Groups(['post:read','post:write','user:read','development:read'])]
    #[Assert\NotBlank]
    #[Assert\Length(min: 4)]
    private string $title;

    /**
     * @ORM\Column(type="text")
     */
    #[Groups(['post:read','post:write','user:read','development:read'])]
    #[Assert\NotBlank]
    #[Assert\Length(min: 5)]
    private string $content;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=false)
     */
    #[Groups(['post:read','post:write','user:read','development:read'])]
    private DateTimeImmutable $createdAt;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    #[Groups(['post:read','post:write','user:read','development:read'])]
    private ?DateTimeImmutable $updatedAt = null;

    /**
     * @ORM\ManyToOne(targetEntity=Development::class, inversedBy="posts", cascade={"persist"})
     * @ORM\JoinColumn(onDelete="CASCADE", nullable=false)
     */
    #[Groups(['post:read'])]
    private Development $development;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="posts", cascade={"persist"})
     * @ORM\JoinColumn(onDelete="CASCADE", nullable=false)
     */
    #[Groups(['post:read','post:write', 'development:read'])]
    private ?User $user;

    /**
     * @ORM\ManyToOne(targetEntity=Post::class, inversedBy="replies", cascade={"persist"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    #[Groups(['post:read','post:write', 'development:read'])]
    private ?Post $parent;

    /**
     * @ORM\OneToMany(targetEntity=Post::class, mappedBy="parent", cascade={"persist"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    #[Groups(['post:read','post:write', 'development:read'])]
    private ?Collection $replies;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
        $this->replies = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;
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

    public function getDevelopment(): Development
    {
        return $this->development;
    }

    public function setDevelopment(Development $development): self
    {
        $this->development = $development;

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

    public function getParent(): ?Post
    {
        return $this->parent;
    }

    public function setParent(?Post $parent): self
    {
        $this->parent = $parent;
        return $this;
    }

    public function getReplies(): ?Collection
    {
        return $this->replies;
    }

    public function addReply(self $reply): self
    {
        if (!$this->replies->contains($reply)) {
            $this->replies[] = $reply;
            $reply->setParent($this);
        }
        return $this;
    }

    public function removeReply(self $reply): self
    {
        $this->replies->removeElement($reply);
        return $this;
    }

    public function __toString()
    {
        return $this->title;
    }
}
