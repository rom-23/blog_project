<?php

namespace App\Controller\SymfonyApp\Post;

use App\Entity\Development\Post;
use App\Form\Development\PostType;
use App\Security\Voter\PostVoter;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{

    /**
     * @param Post $post
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     */
    #[Route('/symfony/development/post/edit/{id<\d+>}', name: 'post_update', methods: ['GET', 'POST'])]
    public function editPost(Post $post, Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted(PostVoter::EDIT, $post);
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $post->setParent(null);
            $em->flush();
            if ($request->isXmlHttpRequest()) {
                return new Response(null, 204);
            }

            return $this->redirect($request->headers->get('referer'));
        }

        return $this->render('_partials/_post.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @param Post $post
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    #[Route('/symfony/development/post/delete/{id<\d+>}', name: 'post_delete', methods: ['DELETE'])]
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
