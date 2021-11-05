<?php

namespace App\Controller\SymfonyApp\Development;

use App\Entity\Development\Development;
use App\Entity\Development\Note;
use App\Entity\Development\Post;
use App\Form\Development\PostType;
use App\Form\Development\SearchDevelopmentType;
use App\Repository\Development\DevelopmentRepository;
use App\Security\Voter\PostVoter;
use App\Service\ManagePaginator;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DevelopmentController extends AbstractController
{
    private const BASE_PATH = 'http://127.0.0.1:8000/apiplatform/';

    /**
     * @param Request $request
     * @param DevelopmentRepository $developmentRepository
     * @return Response
     */
    #[Route('/symfony/developments', name: 'development_list')]
    public function listDevelopments(Request $request, DevelopmentRepository $developmentRepository): Response
    {
        $allDev_json = json_decode(file_get_contents(self::BASE_PATH . 'developments'));
        $form        = $this->createForm(SearchDevelopmentType::class);
        $search      = $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $allDev_json = $developmentRepository->searchDevelopment($search->get('words')->getData());
        }

        return $this->render('symfony-app/development/development-list.html.twig', [
            'developments' => $allDev_json,
            'form'         => $form->createView()
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

    #[Route('/symfony/section/{id<\d+>}', name: 'section_view')]
    public function viewBySection(Request $request): Response
    {
        $dev_json = json_decode(file_get_contents(self::BASE_PATH . 'developments/section/' . $request->attributes->get('id')));
        $html     = $this->renderView('symfony-app/development/development-section.html.twig', [
            'dev' => $dev_json
        ]);
        return $this->json(['view' => $html]);
    }

    /**
     * @param Note $note
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    #[Route('/symfony/development/note/delete/{id<\d+>}', name: 'development_publication_note_delete', methods: ['DELETE'])]
    public function deleteDevelopmentNote(Note $note, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if ($this->isCsrfTokenValid('delete' . $note->getId(), $data['_token'])) {
            $em->remove($note);
            $note->getDevelopment()->setUpdatedAt(new DateTimeImmutable('now'));
            $em->flush();

            return new JsonResponse(['success' => 1]);
        } else {

            return new JsonResponse(['error' => 'Invalid Token'], 400);
        }
    }

}
