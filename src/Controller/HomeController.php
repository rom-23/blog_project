<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{

    /**
     * @return Response
     */
    #[Route('/', name: 'home')]
    public function home(): Response
    {
        return $this->render('home/home.html.twig');
    }

    /**
     * @return Response
     */
    #[Route('/symfony/dev/app', name: 'symfony_dev_app')]
    public function symfonyApp(): Response
    {
        return $this->render('symfony-app/development/home.html.twig');
    }

    /**
     * @return Response
     */
    #[Route('/symfony/model/app', name: 'symfony_model_app')]
    public function symfonyModelApp(): Response
    {
        return $this->render('symfony-app/model/home.html.twig');
    }

    /**
     * @return Response
     */
    #[Route('/app', name: 'front_end_app')]
    public function frontApp(): Response
    {
        return $this->render('front-end-app/home.html.twig');
    }


}
