<?php

namespace App\Controller\RootAdmin;

use App\Entity\Address;
use App\Entity\Development\Note;
use App\Entity\User;
use App\Form\User\AddressType;
use App\Repository\UserRepository;
use App\Handler\UserHandler;
use App\Service\ManagePaginator;
use App\Service\UploadInterface;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
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
    public function deletePost(Note $note, Request $request, EntityManagerInterface $em): JsonResponse
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
