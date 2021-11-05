<?php

namespace App\Controller\SymfonyApp\Note;

use App\Entity\Development\Note;
use App\Form\Development\NoteType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NoteController extends AbstractController
{

    /**
     * @param Note $note
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     */
    #[Route('/note/edit/{id<\d+>}', name: 'note_update', methods: ['GET', 'POST'])]
    public function noteEdit(Note $note, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(NoteType::class, $note);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            if ($request->isXmlHttpRequest()) {
                return new Response(null, 204);
            }
            return $this->redirectToRoute('user_account');
        }

        return $this->render('_partials/_note.html.twig', [
            'form' => $form->createView()
        ]);
    }


}
