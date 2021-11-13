<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\ApiPlatform\UserLoginController;
use App\Entity\Development\Note;
use App\Entity\Development\Post;
use App\Entity\Modelism\Opinion;
use App\Repository\UserRepository;
use DateInterval;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\User\{
    UniqueEmail
};

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields={"email"}, message="There is already a account with this email")
 */
#[ApiResource(
    collectionOperations: [
        'get'  => [
            'security' => 'is_granted("ROLE_USER")'
        ],
        'post' => [
            'security'         => 'is_granted("ROLE_ADMIN")',
            'security_message' => 'Only admins can add users.'
        ]
    ],
    itemOperations: [
        'put'    => [
            'security' => 'is_granted("ROLE_ADMIN", object)'
        ],
        'patch'  => [
            'security' => 'is_granted("ROLE_ADMIN"), object'
        ],
        'delete' => [
            'security' => 'is_granted("ROLE_ADMIN", object)'
        ],
        'get'    => [
            'security' => 'is_granted("ROLE_ADMIN", object)'
        ],
        'me'     => [
            'pagination_enabled' => false,
            'path'               => '/me',
            'method'             => 'get',
            'controller'         => UserLoginController::class,
            'read'               => false,
            'security'           => 'is_granted("ROLE_USER")',
//            'security_message'   => 'Sorry, but you are not the book owner.',
            'openapi_context'    => [
                'security' => ['cookieAuth' => ['']]
            ]
        ]
    ],
    denormalizationContext: ['groups' => ['user:write']],
    normalizationContext: ['groups' => ['user:read']],
//    security: 'is_granted("ROLE_USER")'
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    #[Groups(['user:read', 'user:write', 'post:read', 'note:read'])]
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\NotBlank(message="Please enter an email")
     * @Assert\Email()
     * @UniqueEmail(groups={"create"})
     */
    #[Groups(['user:read', 'user:write', 'post:read', 'note:read', 'development:read'])]
    private string $email;

    /**
     * @var array<int|string, mixed>
     * @ORM\Column(type="json")
     */
    #[Groups(['user:read', 'user:write'])]
    private array $roles;

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="Choose a password!")
     * @Assert\Length(min=4, minMessage="Your password is too short !")
     */
    #[Groups(['user:read', 'user:write'])]
    private string $password = '';

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     * @Assert\NotBlank(message="Please select an image")
     */
    #[Groups(['user:read', 'user:write'])]
    private string $image;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $registrationToken;

    /**
     * @ORM\Column(type="boolean")
     */
    #[Groups(['user:read', 'user:write'])]
    private bool $isVerified;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private DateTimeImmutable $accountMustBeVerifiedBefore;

    /**
     * @ORM\Column(type="datetime_immutable",nullable=true)
     */
    private ?DateTimeImmutable $accountVerifiedAt = null;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    #[Groups(['user:read', 'user:write'])]
    private DateTimeImmutable $registeredAt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $forgotPasswordToken;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private ?DateTimeImmutable $forgotPasswordTokenRequestedAt;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private ?DateTimeImmutable $forgotPasswordTokenMustBeVerifiedBefore;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private ?DateTimeImmutable $forgotPasswordTokenVerifiedAt;

    /**
     * @var Collection<int, Post>
     * @ORM\OneToMany(targetEntity=Post::class, mappedBy="user", orphanRemoval=true, cascade={"persist","remove"})
     */
    #[Groups(['user:read'])]
    private Collection $posts;

    /**
     * @var Collection<int, Note>
     * @ORM\OneToMany(targetEntity=Note::class, mappedBy="user", orphanRemoval=true, cascade={"persist","remove"})
     */
    #[Groups(['user:read'])]
    private Collection $notes;

    /**
     * @var Collection<int, Address>
     * @ORM\OneToMany(targetEntity=Address::class, mappedBy="user", cascade={"persist","remove"})
     */
    #[Groups(['user:read'])]
    private Collection $addresses;

    /**
     * @var Collection|null<int, Opinion>
     * @ORM\OneToMany(targetEntity=Opinion::class, mappedBy="user", orphanRemoval=true, cascade={"persist","remove"})
     */
    #[Groups(['user:read'])]
    private ?Collection $opinions;

    public function __construct()
    {
        $this->posts                       = new ArrayCollection();
        $this->notes                       = new ArrayCollection();
        $this->addresses                   = new ArrayCollection();
        $this->isVerified                  = false;
        $this->registeredAt                = new DateTimeImmutable('now');
        $this->roles                       = ['ROLE_USER'];
        $this->accountMustBeVerifiedBefore = (new DateTimeImmutable('now'))->add(new DateInterval('P1D'));
        $this->opinions                    = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return $this->email;
    }

    public function getRoles(): array
    {
        return $this->roles;

    }

    /**
     * @param array<int|string, mixed> $roles
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    /**
     * @return string
     */
    public function getImage(): string
    {
        return $this->image;
    }

    /**
     * @param string $image
     * @return User
     */
    public function setImage(string $image): User
    {
        $this->image = $image;
        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    /**
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
    }

    public function __toString()
    {
        return $this->getUserIdentifier();
    }

    /**
     * @return Collection<int, Post>
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts[] = $post;
            $post->setUser($this);
        }
        return $this;
    }

    public function removePost(Post $post): self
    {
        $this->posts->removeElement($post);
        return $this;
    }

    /**
     * @return Collection<int, Note>
     */
    public function getNotes(): Collection
    {
        return $this->notes;
    }

    public function addNote(Note $note): self
    {
        if (!$this->notes->contains($note)) {
            $this->notes[] = $note;
            $note->setUser($this);
        }
        return $this;
    }

    public function removeNote(Note $note): self
    {
        $this->notes->removeElement($note);
        return $this;
    }

    /**
     * @return Collection|Address[]
     */
    public function getAddresses(): Collection
    {
        return $this->addresses;
    }

    public function addAddress(Address $address): self
    {
        if (!$this->addresses->contains($address)) {
            $this->addresses[] = $address;
            $address->setUser($this);
        }

        return $this;
    }

    public function removeAddress(Address $address): self
    {
        $this->addresses->removeElement($address);
        return $this;
    }

    /**
     * @return Collection|null<int, Opinion>
     */
    public function getOpinions(): ?Collection
    {
        return $this->opinions;
    }

    public function addOpinion(Opinion $opinion): self
    {
        if (!$this->opinions->contains($opinion)) {
            $this->opinions[] = $opinion;
            $opinion->setUser($this);
        }
        return $this;
    }

    public function removeOpinion(Opinion $opinion): self
    {
        $this->opinions->removeElement($opinion);
        return $this;
    }

    public function getRegistrationToken(): ?string
    {
        return $this->registrationToken;
    }

    public function setRegistrationToken(?string $registrationToken): self
    {
        $this->registrationToken = $registrationToken;

        return $this;
    }

    public function getIsVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getAccountMustBeVerifiedBefore(): DateTimeImmutable
    {
        return $this->accountMustBeVerifiedBefore;
    }

    public function setAccountMustBeVerifiedBefore(DateTimeImmutable $accountMustBeVerifiedBefore): self
    {
        $this->accountMustBeVerifiedBefore = $accountMustBeVerifiedBefore;

        return $this;
    }

    public function getAccountVerifiedAt(): ?DateTimeImmutable
    {
        return $this->accountVerifiedAt;
    }

    public function setAccountVerifiedAt(?DateTimeImmutable $accountVerifiedAt): self
    {
        $this->accountVerifiedAt = $accountVerifiedAt;

        return $this;
    }

    public function getRegisteredAt(): DateTimeImmutable
    {
        return $this->registeredAt;
    }

    public function setRegisteredAt(DateTimeImmutable $registeredAt): self
    {
        $this->registeredAt = $registeredAt;
        return $this;
    }

    public function getForgotPasswordToken(): ?string
    {
        return $this->forgotPasswordToken;
    }

    public function setForgotPasswordToken(?string $forgotPasswordToken): self
    {
        $this->forgotPasswordToken = $forgotPasswordToken;

        return $this;
    }

    public function getForgotPasswordTokenRequestedAt(): ?DateTimeImmutable
    {
        return $this->forgotPasswordTokenRequestedAt;
    }

    public function setForgotPasswordTokenRequestedAt(?DateTimeImmutable $forgotPasswordTokenRequestedAt): self
    {
        $this->forgotPasswordTokenRequestedAt = $forgotPasswordTokenRequestedAt;

        return $this;
    }

    public function getForgotPasswordTokenMustBeVerifiedBefore(): ?DateTimeImmutable
    {
        return $this->forgotPasswordTokenMustBeVerifiedBefore;
    }

    public function setForgotPasswordTokenMustBeVerifiedBefore(?DateTimeImmutable $forgotPasswordTokenMustBeVerifiedBefore): self
    {
        $this->forgotPasswordTokenMustBeVerifiedBefore = $forgotPasswordTokenMustBeVerifiedBefore;

        return $this;
    }

    public function getForgotPasswordTokenVerifiedAt(): ?DateTimeImmutable
    {
        return $this->forgotPasswordTokenVerifiedAt;
    }

    public function setForgotPasswordTokenVerifiedAt(?DateTimeImmutable $forgotPasswordTokenVerifiedAt): self
    {
        $this->forgotPasswordTokenVerifiedAt = $forgotPasswordTokenVerifiedAt;

        return $this;
    }

}
