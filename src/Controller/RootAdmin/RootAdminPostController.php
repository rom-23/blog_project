<?php

namespace App\Controller\RootAdmin;

use App\Entity\Address;
use App\Entity\Development\Post;
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

class RootAdminPostController extends AbstractController
{
    /**
     * @param Post $post
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    #[Route('/root/admin/development/post/delete/{id<\d+>}', name: 'root_admin_post_delete', methods: ['DELETE'])]
    public function deletePost(Post $post, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if ($this->isCsrfTokenValid('delete' . $post->getId(), $data['_token'])) {
            if ($post->getReplies()) {
                foreach ($post->getReplies() as $reply) {
                    $post->removeReply($reply);
                }
            }
            $em->remove($post);
            $post->getDevelopment()->setUpdatedAt(new DateTimeImmutable('now'));
            $em->flush();

            return new JsonResponse(['success' => 1]);
        } else {

            return new JsonResponse(['error' => 'Invalid Token'], 400);
        }
    }
}
