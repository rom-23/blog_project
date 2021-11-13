<?php

namespace App\Entity\Development;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\ApiPlatform\DevSectionController;
use App\Repository\Development\DevelopmentRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\Development\{
    UniqueTitle
};

/**
 * @ORM\Entity(repositoryClass=DevelopmentRepository::class)
 * @ORM\Table(name="development", indexes={@ORM\Index(columns={"title","content"}, flags={"fulltext"})})
 * @UniqueEntity(fields={"title"}, message="There is already a documentation with this title")
 */
#[ApiResource(
    collectionOperations: [
        'get',
        'post',
        'get_by_section' => [
            'method'             => 'GET',
            'path'               => '/developments/section/{id}',
            'controller'         => DevSectionController::class,
            'read'               => false,
            'pagination_enabled' => false,
            'openapi_context'    => [
                'summary'    => 'Get development by section',
                'parameters' => [
                    [
                        'name'        => 'id',
                        'in'          => 'path',
                        'type'        => 'integer',
                        'required'    => true,
                        'description' => 'Filter developments by section'
                    ]
                ],
                'responses'  => [
                    '200' => [
                        'description' => 'OK',
                        'content'     => [
                            'application/json' => [
                                'schema' => [
                                    'type'    => 'integer',
                                    'example' => 2
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ],
    itemOperations: [
        'get',
        'patch',
        'delete',
        'put'
    ],
    denormalizationContext: ['groups' => ['development:write'], 'enable_max_depth' => true],
    normalizationContext: ['groups' => ['development:read'], 'enable_max_depth' => true],
//        security: 'is_granted("ROLE_USER")'
)]
class Development
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    #[Groups(['development:read', 'note:read', 'post:read'])]
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Please enter a title")
     * @Assert\Length(min=5, minMessage="Your title is too short !")
     * @UniqueTitle(groups={"create"})
     */
    #[Groups(['development:read', 'development:write', 'note:read', 'post:read'])]
    private string $title;

    /**
     * @ORM\Column(type="text", nullable=false)
     */
    #[Groups(['development:read', 'development:write'])]
    #[Assert\Length(min: 3)]
    private string $content;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=false)
     */
    #[Groups(['development:read', 'development:write'])]
    private DateTimeImmutable $createdAt;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    #[Groups(['development:read', 'development:write'])]
    private ?DateTimeImmutable $updatedAt = null;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $slug;

    /**
     * @ORM\ManyToOne(targetEntity=Section::class, inversedBy="developments",cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    #[Groups(['development:read', 'development:write'])]
    private Section $section;

    /**
     * @var Collection<int, Tag>
     * @ORM\ManyToMany(targetEntity=Tag::class)
     */
    #[Groups(['development:read', 'development:write'])]
    private Collection $tags;

    /**
     * @var Collection<int, Note>
     * @ORM\OneToMany(targetEntity=Note::class, mappedBy="development", orphanRemoval=true, cascade={"persist","remove"})
     */
    #[Groups(['development:read', 'development:write'])]
    #[Assert\Valid]
    private Collection $notes;

    /**
     * @var Collection<int, Post>
     * @ORM\OneToMany(targetEntity=Post::class, mappedBy="development", orphanRemoval=true, cascade={"persist","remove"})
     */
    #[Groups(['development:read', 'development:write'])]
    private Collection $posts;

    /**
     * @var Collection<int, DevelopmentFile>
     * @ORM\OneToMany(targetEntity=DevelopmentFile::class, mappedBy="developments", orphanRemoval=true, cascade={"persist","remove"})
     */
    #[Groups(['development:read', 'development:write'])]
    private Collection $files;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable('now');
        $this->tags      = new ArrayCollection();
        $this->notes     = new ArrayCollection();
        $this->posts     = new ArrayCollection();
        $this->files     = new ArrayCollection();
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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;
        return $this;
    }

    public function getSection(): Section
    {
        return $this->section;
    }

    public function setSection(Section $section): self
    {
        $this->section = $section;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
        }
        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        $this->tags->removeElement($tag);
        return $this;
    }

    /**
     * @return Collection
     */
    public function getNotes(): Collection
    {
        return $this->notes;
    }

    public function addNote(Note $note): self
    {
        if (!$this->notes->contains($note)) {
            $this->notes[] = $note;
            $note->setDevelopment($this);
        }
        return $this;
    }

    public function removeNote(Note $note): self
    {
        $this->notes->removeElement($note);
        return $this;
    }

    /**
     * @return Collection
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts[] = $post;
            $post->setDevelopment($this);
        }

        return $this;
    }

    public function removePost(Post $post): self
    {
        $this->posts->removeElement($post);
        return $this;
    }

    /**
     * @return Collection
     */
    public function getFiles(): Collection
    {
        return $this->files;
    }

    public function setFiles(ArrayCollection $files): ArrayCollection
    {
        $this->files = $files;
        return $this->files;
    }

    public function addFile(DevelopmentFile $file): self
    {
        if (!$this->files->contains($file)) {
            $this->files[] = $file;
            $file->setDevelopments($this);
        }
        return $this;
    }

    public function removeFile(DevelopmentFile $file): self
    {
        $this->files->removeElement($file);
        return $this;
    }

    #[Pure]
    public function __toString(): string
    {
        return $this->getTitle() ?: '';
    }
}
