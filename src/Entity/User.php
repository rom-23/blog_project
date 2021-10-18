<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\ApiPlatform\UserLoginController;
use App\Entity\Development\Post;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
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
     * @Groups({"user:get"})
     */
    #[Groups(['user:read', 'user:write', 'post:read'])]
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Groups({"user:get"})
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    #[Groups(['user:read', 'user:write', 'post:read', 'development:read'])]
    private ?string $email = null;

    /**
     * @ORM\Column(type="json")
     * @Groups({"user:get"})
     */
    #[Groups(['user:read', 'user:write'])]
    private array $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Groups({"user:get"})
     * @Assert\NotBlank()
     */
    #[Groups(['user:read', 'user:write'])]
    private string $password = '';

    /**
     * @var Collection<int, Post>
     * @ORM\OneToMany(targetEntity=Post::class, mappedBy="user", orphanRemoval=true, cascade={"persist","remove"})
     * @Groups({"user:get"})
     */
    #[Groups(['user:read'])]
    private $posts;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $registrationToken;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $isVerified;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private \DateTimeImmutable $accountMustBeVerifiedBefore;

    /**
     * @ORM\Column(type="datetime_immutable",nullable=true)
     */
    private ?\DateTimeImmutable $accountVerifiedAt = null;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private \DateTimeImmutable $registeredAt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $forgotPasswordToken;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private \DateTimeImmutable $forgotPasswordTokenRequestedAt;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private \DateTimeImmutable $forgotPasswordTokenMustBeVerifiedBefore;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private \DateTimeImmutable $forgotPasswordTokenVerifiedAt;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
        $this->isVerified = false;
        $this->registeredAt = new \DateTimeImmutable('now');
        $this->roles = ['ROLE_USER'];
        $this->accountMustBeVerifiedBefore = (new \DateTimeImmutable('now'))->add(new \DateInterval('P1D'));
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
        return (string)$this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string)$this->email;
    }

    public function getRoles(): array
    {
        return $this->roles;

    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
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
    public function eraseCredentials()
    {
    }

    #[Pure] public function __toString()
    {
        return $this->getUserIdentifier();
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
            $post->setUser($this);
        }
        return $this;
    }

    public function removePost(Post $post): self
    {
        if ($this->posts->removeElement($post)) {
            if ($post->getUser() === $this) {
                $post->setUser(null);
            }
        }
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

    public function getAccountMustBeVerifiedBefore(): \DateTimeImmutable
    {
        return $this->accountMustBeVerifiedBefore;
    }

    public function setAccountMustBeVerifiedBefore(\DateTimeImmutable $accountMustBeVerifiedBefore): self
    {
        $this->accountMustBeVerifiedBefore = $accountMustBeVerifiedBefore;

        return $this;
    }

    public function getAccountVerifiedAt(): ?\DateTimeImmutable
    {
        return $this->accountVerifiedAt;
    }

    public function setAccountVerifiedAt(?\DateTimeImmutable $accountVerifiedAt): self
    {
        $this->accountVerifiedAt = $accountVerifiedAt;

        return $this;
    }

    public function getRegisteredAt(): \DateTimeImmutable
    {
        return $this->registeredAt;
    }

    public function setRegisteredAt(\DateTimeImmutable $registeredAt): self
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

    public function getForgotPasswordTokenRequestedAt(): ?\DateTimeImmutable
    {
        return $this->forgotPasswordTokenRequestedAt;
    }

    public function setForgotPasswordTokenRequestedAt(?\DateTimeImmutable $forgotPasswordTokenRequestedAt): self
    {
        $this->forgotPasswordTokenRequestedAt = $forgotPasswordTokenRequestedAt;

        return $this;
    }

    public function getForgotPasswordTokenMustBeVerifiedBefore(): ?\DateTimeImmutable
    {
        return $this->forgotPasswordTokenMustBeVerifiedBefore;
    }

    public function setForgotPasswordTokenMustBeVerifiedBefore(?\DateTimeImmutable $forgotPasswordTokenMustBeVerifiedBefore): self
    {
        $this->forgotPasswordTokenMustBeVerifiedBefore = $forgotPasswordTokenMustBeVerifiedBefore;

        return $this;
    }

    public function getForgotPasswordTokenVerifiedAt(): ?\DateTimeImmutable
    {
        return $this->forgotPasswordTokenVerifiedAt;
    }

    public function setForgotPasswordTokenVerifiedAt(?\DateTimeImmutable $forgotPasswordTokenVerifiedAt): self
    {
        $this->forgotPasswordTokenVerifiedAt = $forgotPasswordTokenVerifiedAt;

        return $this;
    }
}
