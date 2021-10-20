<?php

namespace App\Controller\RootAdmin;

use App\Entity\Development\Development;
use App\Entity\Development\DevelopmentFile;
use App\Service\ManageUploadFile;
use App\Form\Development\DevelopmentAddType;
use App\Form\Development\DevelopmentEditType;
use App\Repository\Development\DevelopmentRepository;
use DateTime;
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
     * @return Response
     */
    #[Route('/root/admin/dev/list', name: 'root_admin_dev_list', methods: ['GET'])]
    public function index(DevelopmentRepository $devRepository): Response
    {
        return $this->render('root-admin/development/development_list.html.twig', [
            'developments' => $devRepository->findAll()
        ]);
    }

    /**
     * @param Development $development
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     */
    #[Route('/root/admin/dev/{id<\d+>}', name: 'root_admin_dev_edit', methods: ['GET', 'POST'])]
    public function edit(Development $development, Request $request, EntityManagerInterface $em): Response
    {
//        foreach ($development->getFiles() as $file) {
////            $devFile->setName(new File($this->getParameter('app.path.dev_pdf') . '/' . $file->getName()));
////            $devFile->setPath($this->getParameter('app.path.dev_pdf'));
////           array_push($pp,
////                new File($this->getParameter('app.path.dev_pdf') . '/' . $file->getName())
////            );
//        }
        $form = $this->createForm(DevelopmentEditType::class, $development);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if(count($form->get('files')->getData()) > 0){
                $files = $request->files->get('development_edit')['files'];
                $this->manageUploadFile->uploadPdf($files, $development);
            }
            $development->setUpdatedAt(new DateTime('now'));
            $em->flush();
            return $this->redirectToRoute('root_admin_dev_list');
        }
        return $this->render('root-admin/development/development_edit.html.twig', [
            'dev'  => $development,
            'form' => $form->createView()
        ]);
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     */
    #[Route('/root/admin/dev/add', name: 'root_admin_dev_add', methods: ['GET', 'POST'])]
    public function add(Request $request, EntityManagerInterface $em): Response
    {
        $development = new Development();
        $form        = $this->createForm(DevelopmentAddType::class, $development);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
//            dd(count($form->get('files')->getData()) > 0);
//            dd($request->files->get('development_add')['files'][0]['name']);
            if($form->get('files')->getData()) {
                $files = $request->files->get('development_add')['files'];
                $this->manageUploadFile->uploadPdf($files, $development);
            }
            $em->persist($development);
            $em->flush();

            return $this->redirectToRoute('root_admin_dev_list');
        }

        return $this->render('root-admin/development/development_add.html.twig', [
            'form' => $form->createView()
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
            $file->getDevelopments()->setUpdatedAt(new DateTime('now'));
            $em->flush();
            return new JsonResponse(['success' => 1]);
        } else {

            return new JsonResponse(['error' => 'Invalid Token'], 400);
        }
    }

}
