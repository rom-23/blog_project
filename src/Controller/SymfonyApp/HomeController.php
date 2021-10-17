<?php

namespace App\Controller\SymfonyApp;

use App\Entity\Development\Development;
use App\Form\Development\SearchDevelopmentType;
use App\Repository\Development\DevelopmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    private const BASE_PATH = 'http://127.0.0.1:8000/apiplatform/';

    #[Route('/symfony/developments', name: 'development_list')]
    public function list(Request $request, DevelopmentRepository $developmentRepository): Response
    {
        $allDev_json = json_decode(file_get_contents(self::BASE_PATH . 'developments'));
        $form        = $this->createForm(SearchDevelopmentType::class);
        $search      = $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $allDev_json = $developmentRepository->searchDevelopment($search->get('words')->getData());
        }

        return $this->render('symfony-app/development-list.html.twig', [
            'developments' => $allDev_json,
            'form'         => $form->createView()
        ]);
    }

    #[Route('/symfony/development/{id<\d+>}', name: 'development_view')]
    public function view(Development $development, Request $request, EntityManagerInterface $em): Response
    {
        $dev_json = json_decode(file_get_contents(self::BASE_PATH . 'developments/' . $request->attributes->get('id')));
        $html     = $this->renderView('symfony-app/development-view.html.twig', [
            'dev' => $dev_json
        ]);
        return $this->json(['view' => $html]);
    }

    #[Route('/symfony/section/{id<\d+>}', name: 'section_view')]
    public function viewBySection(Request $request): Response
    {
        $dev_json = json_decode(file_get_contents(self::BASE_PATH . 'developments/section/' . $request->attributes->get('id')));
        $html     = $this->renderView('symfony-app/development-section.html.twig', [
            'dev' => $dev_json
        ]);
        return $this->json(['view' => $html]);
    }

}
