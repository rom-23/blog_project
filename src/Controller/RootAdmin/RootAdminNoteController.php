<?php

namespace App\Controller\RootAdmin;

use App\Entity\Development\Note;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RootAdminNoteController extends AbstractController
{
    /**
     * @param Note $note
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    #[Route('/root/admin/development/note/delete/{id<\d+>}', name: 'root_admin_note_delete', methods: ['DELETE'])]
    public function deleteNote(Note $note, Request $request, EntityManagerInterface $em): JsonResponse
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
