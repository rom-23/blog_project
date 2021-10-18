<?php

namespace App\EventListener;

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;

class UserPasswordEncoderEntityListener
{
    private UserPasswordHasherInterface $passwordHasher;

    /**
     * @param UserPasswordHasherInterface $passwordHasher
     */
    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function prePersist(User $user, LifecycleEventArgs $args): void
    {
        $this->encodeUserPassword($user, $user->getPassword());
    }

    public function encodeUserPassword(User $user, string $password): void
    {
        $user->setPassword($this->passwordHasher->hashPassword($user, $password));
    }

    public function preUpdate(User $user, LifecycleEventArgs $args): void
    {
        $userChanges = $args->getEntityChangeSet();
        if(array_key_exists('password', $userChanges)){
            $this->encodeUserPassword($user, $userChanges['password'][1]);
        }
    }
}
