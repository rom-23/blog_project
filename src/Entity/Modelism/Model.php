<?php

namespace App\Entity\Modelism;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Cocur\Slugify\Slugify;
use Exception;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Modelism\ModelRepository")
 * @UniqueEntity("name")
 * @Vich\Uploadable
 */
class Model
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"get"})
     */
    private ?int $id = null;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=255, nullable=true))
     * @Groups({"get"})
     */
    private ?string $filename = null;

    /**
     * @var File|null
     * @Assert\Image(mimeTypes="image/jpeg")
     * @Vich\UploadableField(mapping="model_images", fileNameProperty="filename")
     */
    private ?File $imageFile = null;

    /**
     * @var File|null
     * @Assert\Image(mimeTypes="image/jpeg")
     * @Vich\UploadableField(mapping="model_image_original", fileNameProperty="original")
     */
    private ?File $originalFile = null;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $original = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"get"})
     */
    private ?string $name = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"get"})
     */
    private ?string $description = null;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private ?float $price;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=false)
     */
    private DateTimeImmutable $createdAt;

    /**
     * @var Collection<int, Option>
     * @ORM\ManyToMany(targetEntity="App\Entity\Modelism\Option", inversedBy="models", cascade={"persist"})
     */
    private Collection $options;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private ?DateTimeImmutable $updated_at = null;

    /**
     * @var Collection<int, Image>
     * @ORM\ManyToMany(targetEntity="App\Entity\Modelism\Image", mappedBy="models", cascade={"persist", "remove"})
     * @Groups({"get"})
     */
    private Collection $images;

    /**
     * @var Collection<int, Category>
     * @ORM\ManyToMany(targetEntity="App\Entity\Modelism\Category", inversedBy="models", cascade={"persist"})
     * @Groups({"get"})
     */
    private Collection $categories;

    /**
     * Model constructor.
     * @throws Exception
     */
    public function __construct()
    {
        $this->createdAt  = new DateTimeImmutable('now');
        $this->options    = new ArrayCollection();
        $this->images     = new ArrayCollection();
        $this->categories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSlug(): string
    {
        return (new Slugify())->slugify($this->name);
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getFormattedPrice(): string
    {
        return number_format($this->price, 0, '', ' ');
    }

    /**
     * @return string|null
     */
    public function getOriginal(): ?string
    {
        return $this->original;
    }

    /**
     * @param null|string $originalFile
     * @return Model
     */
    public function setOriginal(?string $originalFile): Model
    {
        $this->original = $originalFile;
        return $this;
    }

    /**
     * @return File|null
     */
    public function getOriginalFile(): ?File
    {
        return $this->originalFile;
    }

    /**
     * @param null|File $originalFile
     * @return Model
     */
    public function setOriginalFile(?File $originalFile): Model
    {
        $this->originalFile = $originalFile;
        if ($this->originalFile instanceof UploadedFile) {
            $this->updated_at = new DateTimeImmutable('now');
        }
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
        return $this->updated_at;
    }

    public function setUpdatedAt(?DateTimeImmutable $updated_at): self
    {
        $this->updated_at = $updated_at;

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
     * @return string|null
     */
    public function getFilename(): ?string
    {
        return $this->filename;
    }

    /**
     * @param null|string $filename
     * @return Model
     */
    public function setFilename(?string $filename): Model
    {
        $this->filename = $filename;
        return $this;
    }

    /**
     * @return File|null
     */
    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    /**
     * @param null|File $imageFile
     * @return Model
     */
    public function setImageFile(?File $imageFile): Model
    {
        $this->imageFile = $imageFile;
        if ($this->imageFile instanceof UploadedFile) {
            $this->updated_at = new DateTimeImmutable('now');
        }
        return $this;
    }

    /**
     * @return Collection<int, Image>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->addModel($this);
        }

        return $this;
    }

    public function removeImage(Image $image): self
    {
        if ($this->images->contains($image)) {
            $this->images->removeElement($image);
            $image->removeModel($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
            $category->addModel($this);
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        if ($this->categories->contains($category)) {
            $this->categories->removeElement($category);
            $category->removeModel($this);
        }

        return $this;
    }

//    public function __toString(): ?string
//    {
//        return $this->getName();
//    }
}
