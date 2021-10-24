<?php

namespace App\Security\Voter;

use App\Entity\Development\Post;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class DevelopmentVoter extends Voter
{
    public const EDIT = 'edit';

    protected function supports(string $attribute, $subject)
    {
        if (!$subject instanceof Post) {
            return false;
        }
        if ($attribute == self::EDIT) {
            return true;
        }
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token)
    {
        /** @var User $user $user */
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }
        switch ($attribute){
            case self::EDIT;
            return $user === $subject->getUser();
        }
    }
}
