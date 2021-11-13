<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\AddressRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=AddressRepository::class)
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
    denormalizationContext: ['groups' => ['address:write'], 'enable_max_depth' => true],
    normalizationContext: ['groups' => ['address:read'], 'enable_max_depth' => true],
)]
class Address
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    #[Groups(['address:read', 'address:write', 'user:read'])]
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Groups(['address:read', 'address:write', 'user:read'])]
    private string $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Groups(['address:read', 'address:write', 'user:read'])]
    private string $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Groups(['address:read', 'address:write', 'user:read'])]
    private string $lastname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Groups(['address:read', 'address:write', 'user:read'])]
    private string $address;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Groups(['address:read', 'address:write', 'user:read'])]
    private string $postal;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Groups(['address:read', 'address:write', 'user:read'])]
    private string $city;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Groups(['address:read', 'address:write', 'user:read'])]
    private string $country;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=false)
     */
    #[Groups(['address:read', 'address:write', 'user:read'])]
    private DateTimeImmutable $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="addresses")
     */
    #[Groups(['address:read'])]
    private User $user;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable('now');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
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

    public function getFirstname(): string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;
        return $this;
    }

    public function getLastname(): string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;
        return $this;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;
        return $this;
    }

    public function getPostal(): string
    {
        return $this->postal;
    }

    public function setPostal(string $postal): self
    {
        $this->postal = $postal;
        return $this;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;
        return $this;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;
        return $this;
    }

    public function __toString()
    {
        return $this->name . '[br]' . $this->address . '[br]' . $this->city . '-' . $this->country;
    }
}
