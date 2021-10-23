<?php

namespace App\Controller\ApiPlatform;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class UserLoginController extends AbstractController
{
    /**
     * @return UserInterface|null
     */
    public function __invoke(): ?UserInterface
    {
        return $this->security->getUser();
    }

    public function __construct(private Security $security)
    {
    }
}
