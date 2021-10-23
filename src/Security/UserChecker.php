<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{

    public function checkPreAuth(UserInterface $user): void
    {
        if(!$user instanceof User){
            return;
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
        if(!$user instanceof User){
            return;
        }
        if(!$user->getIsVerified()){
            throw new CustomUserMessageAccountStatusException("Your account is not activated, check your email before {$user->getAccountMustBeVerifiedBefore()->format('d/m/Y at H\hi')}");
        }
    }
}
