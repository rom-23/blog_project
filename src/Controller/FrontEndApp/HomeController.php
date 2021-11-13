<?php

namespace App\Controller\FrontEndApp;

use App\Repository\Modelism\CategoryRepository;
use App\Repository\Modelism\ModelRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{

    /**
     * @return Response
     */
    #[Route('/front-end-app/vue', name: 'front_end_vue_app')]
    public function frontEndVueApp(): Response
    {
        return $this->render('front-end-app/vue/home-vue.html.twig');
    }

    /**
     * @return Response
     */
    #[Route('/front-end-app/react', name: 'front_end_react_app')]
    public function frontEndReactApp(): Response
    {
        return $this->render('front-end-app/react/home-react.html.twig');
    }
}
