<?php

namespace App\Controller\SymfonyApp\Development;

use App\Entity\Development\Development;
use App\Entity\Development\Post;
use App\Event\PostEvent;
use App\Form\Development\PostType;
use App\Form\Development\SearchDevelopmentType;
use App\Repository\Development\DevelopmentRepository;
use App\Service\ManagePaginator;
use Doctrine\ORM\EntityManagerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DevelopmentPublicationController extends AbstractController
{
    /**
     * @var EventDispatcherInterface
     */
    private EventDispatcherInterface $eventDispatcher;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param DevelopmentRepository $devRepository
     * @param Request $request
     * @param ManagePaginator $managePaginator
     * @return Response
     */
    #[Route('/symfony/development/publications', name: 'development_publication')]
    public function listPublication(DevelopmentRepository $devRepository, Request $request, ManagePaginator $managePaginator): Response
    {
        $limit        = $request->get('limit', 10);
        $page         = $request->get('page', 1);
        $developments = $managePaginator->paginate($devRepository->paginateDevelopments(), $page, $limit);
        $form         = $this->createForm(SearchDevelopmentType::class);
        $search       = $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $developments = $managePaginator->paginate($devRepository->searchDevelopment($search->get('words')->getData()), $page, $limit);
        }
        return $this->render('symfony-app/development/publication-list.html.twig', [
            'developments' => $developments,
            'pages'        => $managePaginator->lastPage($developments),
            'page'         => $page,
            'limit'        => $limit,
            'range'        => $managePaginator->rangePaginator($page, $developments),
            'form'         => $form->createView()
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
            $event = new PostEvent($post);
            $this->eventDispatcher->dispatch($event);
            $this->addFlash('message', 'Your comment has been sent');

            return $this->redirectToRoute('development_publication_view', ['id' => $development->getId()]);
        }

        return $this->render('symfony-app/development/publication-view.html.twig', [
            'dev'      => $development,
            'postForm' => $postForm->createView()
        ]);
    }

}
