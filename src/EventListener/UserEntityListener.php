<?php

namespace App\EventListener;

use App\Entity\User;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

class UserEntityListener
{
    /**
     * @var UserPasswordHasherInterface
     */
    private UserPasswordHasherInterface $passwordHasher;

    /**
     * @var KernelInterface
     */
    private KernelInterface $kernel;

    /**
     * @param UserPasswordHasherInterface $passwordHasher
     * @param KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel, UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
        $this->kernel         = $kernel;
    }

    /**
     * @param User $user
     * @param LifecycleEventArgs $args
     */
    public function prePersist(User $user, LifecycleEventArgs $args): void
    {
        $this->encodeUserPassword($user, $user->getPassword());
    }

    /**
     * @param User $user
     * @param string $password
     */
    public function encodeUserPassword(User $user, string $password): void
    {
        $user->setPassword($this->passwordHasher->hashPassword($user, $password));
    }

    /**
     * @param User $user
     * @param LifecycleEventArgs $args
     */
    public function preUpdate(User $user, LifecycleEventArgs $args): void
    {
        $userChanges = $args->getEntityChangeSet();
        if (array_key_exists('password', $userChanges)) {
            $this->encodeUserPassword($user, $userChanges['password'][1]);
        }
        if(array_key_exists('image', $userChanges)){
            $filesystem = new Filesystem();
            $filesystem->remove(['', $this->kernel->getProjectDir() . '/public/uploads/user-image/' . $userChanges['image'][0], '']);
        }
    }

    /**
     * @param User $user
     * @param LifecycleEventArgs $args
     */
    public function preRemove(User $user, LifecycleEventArgs $args): void
    {
        $filesystem = new Filesystem();
        $filesystem->remove(['', $this->kernel->getProjectDir() . '/public/uploads/user-image/' . $user->getImage(), '']);
    }
}
