<?php

namespace App\Controller\SymfonyApp;

use App\Entity\Development\Development;
use App\Entity\Development\Post;
use App\Form\Development\PostType;
use App\Repository\Development\DevelopmentRepository;
use App\Security\Voter\DevelopmentVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PublishController extends AbstractController
{
    #[Route('/symfony/development/publication', name: 'development_publication')]
    public function list(DevelopmentRepository $developmentRepository): Response
    {
        $developments = $developmentRepository->findAll();
        return $this->render('symfony-app/publication-list.html.twig', [
            'developments' => $developments
        ]);
    }

    #[Route('/symfony/development/publication/{id<\d+>}', name: 'development_publication_view')]
    public function view(Development $development, Request $request, EntityManagerInterface $em): Response
    {
        $post     = new Post();
        $postForm = $this->createForm(PostType::class, $post);
        $postForm->handleRequest($request);
        if ($postForm->isSubmitted() && $postForm->isValid()) {
            $parentId = $postForm->get('parentid')->getData();//champs Hidden rempli par JS
            if ($parentId != null) {
                $parent = $em->getRepository(Post::class)->find($parentId);
            }
            $post->setParent($parent ?? null);
            $post->setDevelopment($development);
            $post->setUser($this->getUser());
            $em->persist($post);
            $em->flush();
            $this->addFlash('message', 'Your comment has been sent');
            return $this->redirectToRoute('development_publication_view', ['id' => $development->getId()]);
        }
        return $this->render('symfony-app/publication-view.html.twig', [
            'dev'      => $development,
            'postForm' => $postForm->createView()
        ]);
    }

    #[Route('/symfony/development/publication/edit/{id<\d+>}', name: 'development_publication_edit')]
    public function editPost(Post $post, Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted(DevelopmentVoter::EDIT, $post);
        $postEditForm = $this->createForm(PostType::class, $post);
        $postEditForm->handleRequest($request);
        if ($postEditForm->isSubmitted() && $postEditForm->isValid()) {
            $post->setParent(null);
            $post->setDevelopment($post->getDevelopment());
            $post->setUser($this->getUser());
            $em->flush();
            $this->addFlash('message', 'Your comment has been updated');
            return $this->redirectToRoute('development_publication_view', ['id' => $post->getDevelopment()->getId()]);
        }
        return $this->render('symfony-app/publication-edit.html.twig', [
            'post'      => $post,
            'postEditForm' => $postEditForm->createView()
        ]);
    }
}
