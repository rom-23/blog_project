<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function home(): Response
    {
        return $this->render('home/home.html.twig');
    }

    /**
     * @Route("/symfony", name="symfony_app")
     */
    public function symfonyApp(): Response
    {
        return $this->render('symfony-app/home.html.twig');
    }

    /**
     * @Route("/app", name="front_end_app")
     */
    public function frontApp(): Response
    {
        return $this->render('symfony-app/model/home.html.twig');
    }


}
