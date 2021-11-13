<?php

namespace App\Entity\Development;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\Development\SectionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass=SectionRepository::class)
 * @ORM\Table(name="section",indexes={
 *     @ORM\Index(
 *     columns={"title"},
 *     flags={"fulltext"}
 *     )})
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
    denormalizationContext: ['groups' => ['section:write'], 'enable_max_depth' => true],
    normalizationContext: ['groups' => ['section:read'], 'enable_max_depth' => true],
)]
class Section
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    #[Groups(['section:read', 'development:read'])]
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Groups(['section:read', 'section:write', 'development:read'])]
    #[Assert\NotBlank]
    private string $title;

    /**
     * @var Collection<int, Development>
     * @ORM\OneToMany(targetEntity=Development::class, mappedBy="section")
     */
    private Collection $developments;

    #[Pure]
    public function __construct()
    {
        $this->developments = new ArrayCollection();
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

    /**
     * @return <Collection<int, Development>
     */
    public function getDevelopments(): Collection
    {
        return $this->developments;
    }

    public function __toString()
    {
        return $this->getTitle();
    }
}
