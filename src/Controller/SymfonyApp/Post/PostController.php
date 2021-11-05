<?php

namespace App\Controller\SymfonyApp\Post;

use App\Entity\Development\Post;
use App\Form\Development\PostType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    #[Route('/post/edit/{id<\d+>}', name: 'post_update', methods: ['GET', 'POST'])]
    public function postEdit(Post $post, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $post->setParent(null);
            $em->flush();
            if ($request->isXmlHttpRequest()) {
                return new Response(null, 204);
            }
            return $this->redirectToRoute('user_account');
        }

        return $this->render('_partials/_post.html.twig', [
            'form' => $form->createView()
        ]);
    }


}
