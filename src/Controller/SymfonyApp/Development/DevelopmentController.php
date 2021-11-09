<?php

namespace App\Controller\SymfonyApp\Development;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DevelopmentController extends AbstractController
{
    private const BASE_PATH = 'http://127.0.0.1:8000/apiplatform/';

    /**
     * @return Response
     */
    #[Route('/symfony/developments', name: 'development_list')]
    public function listDevelopments(): Response
    {
        $allDev_json = json_decode(file_get_contents(self::BASE_PATH . 'developments'));
        return $this->render('symfony-app/development/development-list.html.twig', [
            'developments' => $allDev_json
        ]);
    }

    #[Route('/symfony/development/{id<\d+>}', name: 'development_view')]
    public function viewDevelopment(Request $request): Response
    {
        $dev_json = json_decode(file_get_contents(self::BASE_PATH . 'developments/' . $request->attributes->get('id')));
        $html     = $this->renderView('symfony-app/development/development-view.html.twig', [
            'dev' => $dev_json
        ]);
        return $this->json(['view' => $html]);
    }

    #[Route('/symfony/development/section/{id<\d+>}', name: 'development_section_view')]
    public function viewDevelopmentBySection(Request $request): Response
    {
        $dev_json = json_decode(file_get_contents(self::BASE_PATH . 'developments/section/' . $request->attributes->get('id')));
        $html     = $this->renderView('symfony-app/development/development-section.html.twig', [
            'dev' => $dev_json
        ]);
        return $this->json(['view' => $html]);
    }

}
