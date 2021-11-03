<?php

namespace App\Entity\Modelism;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Modelism\ImageRepository")
 */
class Image
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"get"})
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Groups({"get"})
     */
    private string $name;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=false)
     */
    private DateTimeImmutable $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=Model::class, inversedBy="images")
     * @ORM\JoinColumn(onDelete="CASCADE", nullable=false)
     */
    private Model $models;

    public function __construct()
    {
        $this->createdAt  = new DateTimeImmutable();
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

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getModels(): Model
    {
        return $this->models;
    }

    public function setModels(Model $models): self
    {
        $this->models = $models;

        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }
}
