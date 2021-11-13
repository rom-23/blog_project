<?php

namespace App\Entity\Modelism;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Modelism\ModelRepository")
 */
class Model
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"model:get"})
     */
    private ?int $id = null;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=255, nullable=true))
     * @Groups({"model:get"})
     */
    private ?string $thumbnail;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Assert\NotBlank(message="Please enter a name")
     * @Assert\Length(min=5, minMessage="Your name is too short !")
     * @Groups({"model:get"})
     */
    private string $name;

    /**
     * @ORM\Column(type="text", nullable=false)
     * @Assert\NotBlank(message="Please enter a description")
     * @Assert\Length(min=5, minMessage="Your description is too short !")
     * @Groups({"model:get"})
     */
    private string $description;

    /**
     * @ORM\Column(type="float", nullable=false)
     * @Assert\NotBlank(message="Please enter a price")
     * @Groups({"model:get"})
     */
    private float $price;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=false)
     * @Groups({"model:get"})
     */
    private DateTimeImmutable $createdAt;

    /**
     * @var Collection<int, Option>
     * @ORM\ManyToMany(targetEntity="App\Entity\Modelism\Option", inversedBy="models", cascade={"persist"})
     * @Groups({"model:get"})
     */
    private Collection $options;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     * @Groups({"model:get"})
     */
    private ?DateTimeImmutable $updated_at = null;

    /**
     * @var Collection<int, Category>
     * @ORM\ManyToMany(targetEntity="App\Entity\Modelism\Category", inversedBy="models", cascade={"persist"})
     * @Groups({"model:get"})
     */
    private Collection $categories;

    /**
     * @var Collection<int, Image>
     * @ORM\OneToMany(targetEntity=Image::class, mappedBy="models", orphanRemoval=true, cascade={"persist","remove"})
     * @Groups({"model:get"})
     */
    private Collection $images;

    /**
     * @var Collection<int, Opinion>
     * @ORM\OneToMany(targetEntity=Opinion::class, mappedBy="model", orphanRemoval=true, cascade={"persist","remove"})
     * @Groups({"model:get"})
     */
    private Collection $opinions;

    public function __construct()
    {
        $this->createdAt  = new DateTimeImmutable('now');
        $this->options    = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->images     = new ArrayCollection();
        $this->opinions   = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;
        return $this;
    }

    public function getFormattedPrice(): string
    {
        return number_format($this->price, 0, '', ' ');
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
        return $this->updated_at;
    }

    public function setUpdatedAt(?DateTimeImmutable $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getThumbnail(): ?string
    {
        return $this->thumbnail;
    }

    /**
     * @param null|string $thumbnail
     * @return Model
     */
    public function setThumbnail(?string $thumbnail): self
    {
        $this->thumbnail = $thumbnail;
        return $this;
    }

    /**
     * @return Collection<int, Option>
     */
    public function getOptions(): Collection
    {
        return $this->options;
    }

    public function addOption(Option $option): self
    {
        if (!$this->options->contains($option)) {
            $this->options[] = $option;
//            $option->addModel($this);
        }
        return $this;
    }

    public function removeOption(Option $option): self
    {
        if ($this->options->contains($option)) {
            $this->options->removeElement($option);
//            $option->removeModel($this);
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
//            $category->addModel($this);
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        if ($this->categories->contains($category)) {
            $this->categories->removeElement($category);
//            $category->removeModel($this);
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function setImages(ArrayCollection $image): ArrayCollection
    {
        $this->images = $image;
        return $this->images;
    }

    public function addImage(Image $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setModels($this);
        }
        return $this;
    }

    public function removeImage(Image $image): self
    {
        $this->images->removeElement($image);
        return $this;
    }

    /**
     * @return Collection|Opinion[]
     */
    public function getOpinions(): Collection
    {
        return $this->opinions;
    }

    public function addOpinion(Opinion $opinion): self
    {
        if (!$this->opinions->contains($opinion)) {
            $this->opinions[] = $opinion;
            $opinion->setModel($this);
        }

        return $this;
    }

    public function removeOpinion(Opinion $opinion): self
    {
        if ($this->opinions->removeElement($opinion)) {
            if ($opinion->getModel() === $this) {
                $opinion->setModel(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->getName() ?: '';
    }

}
