<?php

namespace App\Entity\Development;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\Development\SectionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=SectionRepository::class)
 * @ORM\Table(name="section",
 *              indexes={@ORM\Index(columns={"title"},
 *                       flags={"fulltext"}
 *                       )}
 *            )
 */
#[ApiResource(
    denormalizationContext: ['groups' => ['section:write']],
    normalizationContext: ['groups' => ['section:read']]
)]
class Section
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    #[Groups(['section:read','development:read'])]
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Groups(['section:read','section:write', 'development:read'])]
    private string $title;

    /**
     * @ORM\OneToMany(targetEntity=Development::class, mappedBy="section")
     */
    private $developments;

    #[Pure] public function __construct()
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
     * @return Collection
     */
    public function getDevelopments(): Collection
    {
        return $this->developments;
    }

    public function addDevelopment(Development $development): self
    {
        if (!$this->developments->contains($development)) {
            $this->developments[] = $development;
            $development->setSection($this);
        }

        return $this;
    }

    public function removeDevelopment(Development $development): self
    {
        if ($this->developments->removeElement($development)) {
            // set the owning side to null (unless already changed)
            if ($development->getSection() === $this) {
                $development->setSection(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->getTitle();
    }
}
