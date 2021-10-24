<?php

namespace App\Controller\RootAdmin;

use App\Entity\Development\Development;
use App\Entity\Development\DevelopmentFile;
use App\Handler\DevelopmentHandler;
use App\Security\Voter\DevelopmentVoter;
use App\Service\ManagePaginator;
use App\Service\ManageUploadFile;
use App\Repository\Development\DevelopmentRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RootAdminDevController extends AbstractController
{
    /**
     * @var ManageUploadFile
     */
    private ManageUploadFile $manageUploadFile;

    /**
     * @param ManageUploadFile $manageUploadFile
     */
    public function __construct(ManageUploadFile $manageUploadFile)
    {
        $this->manageUploadFile = $manageUploadFile;
    }

    /**
     * @param DevelopmentRepository $devRepository
     * @param Request $request
     * @param ManagePaginator $managePaginator
     * @return Response
     */
    #[Route('/root/admin/dev/list', name: 'root_admin_dev_list', methods: ['GET'])]
    public function index(DevelopmentRepository $devRepository, Request $request, ManagePaginator $managePaginator): Response
    {
        $limit = $request->get('limit', 5);
        $page  = $request->get('page', 1);

        $developments = $managePaginator->paginate($devRepository->paginateDevelopments(),$page,$limit);

        return $this->render('root-admin/development/development_list.html.twig', [
            'developments' => $developments,
            'pages'        => $managePaginator->lastPage($developments),
            'page'         => $page,
            'limit'        => $limit,
            'range'        => $managePaginator->rangePaginator($page, $developments)
        ]);
    }

    /**
     * @param Development $development
     * @param DevelopmentHandler $developmentHandler
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     */
    #[Route('/root/admin/dev/{id<\d+>}', name: 'root_admin_dev_edit', methods: ['GET', 'POST'])]
    public function edit(Development $development, DevelopmentHandler $developmentHandler, Request $request, EntityManagerInterface $em): Response
    {
        if ($developmentHandler->handle($request, $development)) {
            return $this->redirectToRoute('root_admin_dev_list');
        }
        return $this->render('root-admin/development/development_edit.html.twig', [
            'dev'  => $development,
            'form' => $developmentHandler->createView()
        ]);
    }

    /**
     * @param DevelopmentHandler $developmentHandler
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     */
    #[Route('/root/admin/dev/add', name: 'root_admin_dev_add', methods: ['GET', 'POST'])]
    public function add(DevelopmentHandler $developmentHandler, Request $request, EntityManagerInterface $em): Response
    {
        $development = new Development();
        if ($developmentHandler->handle($request, $development)) {
            return $this->redirectToRoute('root_admin_dev_list');
        }
        return $this->render('root-admin/development/development_add.html.twig', [
            'form' => $developmentHandler->createView()
        ]);
    }

    /**
     * @param DevelopmentFile $file
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    #[Route('/root/admin/dev/delete/file/{id}', name: 'root_admin_dev_delete_file', methods: ['DELETE'])]
    public function deleteFile(DevelopmentFile $file, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if ($this->isCsrfTokenValid('delete' . $file->getId(), $data['_token'])) {
            $fileName = $file->getName();
            // On supprime le fichier
            unlink($this->getParameter('app.path.dev_pdf') . '/' . $fileName);
            $em->remove($file);
            $file->getDevelopments()->setUpdatedAt(new DateTimeImmutable('now'));
            $em->flush();
            return new JsonResponse(['success' => 1]);
        } else {

            return new JsonResponse(['error' => 'Invalid Token'], 400);
        }
    }

}
