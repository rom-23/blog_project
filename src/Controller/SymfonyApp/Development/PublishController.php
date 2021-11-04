<?php

namespace App\Controller\SymfonyApp\Development;

use App\Entity\Development\Development;
use App\Entity\Development\DevelopmentFile;
use App\Entity\Development\Note;
use App\Entity\Development\Post;
use App\Form\Development\PostType;
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

class PublishController extends AbstractController
{
    /**
     * @param DevelopmentRepository $devRepository
     * @param Request $request
     * @param ManagePaginator $managePaginator
     * @return Response
     */
    #[Route('/symfony/development/publication', name: 'development_publication')]
    public function listPublication(DevelopmentRepository $devRepository,Request $request, ManagePaginator $managePaginator): Response
    {
        $limit        = $request->get('limit', 10);
        $page         = $request->get('page', 1);
        $developments = $managePaginator->paginate($devRepository->paginateDevelopments(), $page, $limit);
        return $this->render('symfony-app/development/publication-list.html.twig', [
            'developments' => $developments,
            'pages'        => $managePaginator->lastPage($developments),
            'page'         => $page,
            'limit'        => $limit,
            'range'        => $managePaginator->rangePaginator($page, $developments)
        ]);
    }

    #[Route('/symfony/development/publication/{id<\d+>}', name: 'development_publication_view')]
    public function viewPublication(Development $development, Request $request, EntityManagerInterface $em): Response
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

        return $this->render('symfony-app/development/publication-view.html.twig', [
            'dev'      => $development,
            'postForm' => $postForm->createView()
        ]);
    }

    #[Route('/symfony/development/post/edit/{id<\d+>}', name: 'development_post_edit')]
    public function editPost(Post $post, Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted(PostVoter::EDIT, $post);
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
        return $this->render('symfony-app/development/publication-edit.html.twig', [
            'post'         => $post,
            'postEditForm' => $postEditForm->createView()
        ]);
    }

    /**
     * @param Post $post
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    #[Route('/symfony/development/post/delete/{id<\d+>}', name: 'development_post_delete', methods: ['DELETE'])]
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

    /**
     * @param Note $note
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    #[Route('/symfony/development/note/delete/{id<\d+>}', name: 'development_note_delete', methods: ['DELETE'])]
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
