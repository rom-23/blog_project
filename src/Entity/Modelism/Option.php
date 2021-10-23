<?php

namespace App\Entity\Modelism;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Modelism\OptionRepository")
 * @ORM\Table(name="`option`")
 */
class Option
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $name = null;

    /**
     * @var Collection<int, Model>
     * @ORM\ManyToMany(targetEntity="App\Entity\Modelism\Model", mappedBy="options")
     */
    private Collection $models;

    #[Pure] public function __construct()
    {
        $this -> models = new ArrayCollection();
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

    /**
     * @return Collection|Option
     */
    public function getModels(): Collection
    {
        return $this -> models;
    }

    #[Pure] public function __toString()
    {
        return $this->getName() ?: '';
    }
}
