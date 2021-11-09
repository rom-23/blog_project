<?php

namespace App\Controller\SymfonyApp\Opinion;

use App\Entity\Modelism\Opinion;
use App\Form\Modelism\OpinionType;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OpinionController extends AbstractController
{
    /**
     * @param Opinion $opinion
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     */
    #[Route('/symfony/model/opinion/edit/{id<\d+>}', name: 'opinion_update', methods: ['GET', 'POST'])]
    public function editOpinion(Opinion $opinion, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(OpinionType::class, $opinion);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            if ($request->isXmlHttpRequest()) {
                return new Response(null, 204);
            }

            return $this->redirect($request->headers->get('referer'));
        }

        return $this->render('_partials/_opinion.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @param Opinion $opinion
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    #[Route('/symfony/model/opinion/delete/{id<\d+>}', name: 'opinion_delete', methods: ['DELETE'])]
    public function deleteOpinion(Opinion $opinion, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if ($this->isCsrfTokenValid('delete' . $opinion->getId(), $data['_token'])) {
            $em->remove($opinion);
            $em->flush();

            return new JsonResponse(['success' => 1]);
        } else {

            return new JsonResponse(['error' => 'Invalid Token'], 400);
        }
    }

}
