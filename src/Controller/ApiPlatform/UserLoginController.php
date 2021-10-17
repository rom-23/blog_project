<?php

namespace App\Controller\ApiPlatform;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class UserLoginController extends AbstractController
{
    public function __invoke()
    {
        $user = $this->security->getUser();
        return $user;
    }

    public function __construct(private Security $security)
    {
    }
}
