<?php

namespace App\Entity;

use ApiPlatform\Core\Action\NotFoundAction;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\ApiPlatform\UserLoginController;
use App\Entity\Development\Post;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
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

    #[Pure] public function __construct()
    {
        $this->posts = new ArrayCollection();
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

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
//        $roles = $this->roles;
//        // guarantee every user at least has ROLE_USER
//        $roles[] = 'ROLE_USER';
//
//        return array_unique($roles);
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
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
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
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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
            // set the owning side to null (unless already changed)
            if ($post->getUser() === $this) {
                $post->setUser(null);
            }
        }

        return $this;
    }
}
