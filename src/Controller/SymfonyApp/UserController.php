<?php

namespace App\Controller\SymfonyApp;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route(path: '/symfony-app/users', name: 'app_user')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        return $this->render('security/index.html.twig');
    }
}
