<?php

namespace App\Controller;

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
     * @param CategoryRepository $categoryRepository
     * @return Response
     */
    #[Route('/symfony/model/app', name: 'symfony_model_app', methods: 'GET')]
    public function symfonyModelApp(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAll();
        return $this->render('symfony-app/model/home.html.twig', [
            'categories' => $categories
        ]);
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
