<?php

namespace App\Controller\RootAdmin;

use App\Entity\Development\Development;
use App\Form\Development\DevelopmentAddType;
use App\Form\Development\DevelopmentEditType;
use App\Repository\Development\DevelopmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RootAdminDevController extends AbstractController
{
    #[Route('/root/admin/dev/list', name: 'root_admin_dev_list')]
    public function index(DevelopmentRepository $devRepository): Response
    {
        return $this->render('root-admin/development/development_list.html.twig', [
            'developments' => $devRepository->findAll()
        ]);
    }

    #[Route('/root/admin/dev/{id<\d+>}', name: 'root_admin_dev_edit')]
    public function edit(Development $dev, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(DevelopmentEditType::class, $dev);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $request->files->get('development_edit')['file'];
            $dev->setFile($file);
            $dev->setUpdatedAt(new \DateTime());
            $em->flush();
            return $this->redirectToRoute('root_admin_dev_list');
        }
        return $this->render('root-admin/development/development_edit.html.twig', [
            'dev'  => $dev,
            'form' => $form->createView()
        ]);
    }

    #[Route('/root/admin/dev/add', name: 'root_admin_dev_add')]
    public function devAdd(Request $request, EntityManagerInterface $em): Response
    {
        $development = new Development();
        $form        = $this->createForm(DevelopmentAddType::class, $development);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $request->files->get('development_add')['file'];
            $development->setFile($file);
            $development->setUpdatedAt(new \DateTime());
            $em->persist($development);
            $em->flush();
            return $this->redirectToRoute('root_admin_dev_list');
        }

        return $this->render('root-admin/development/development_add.html.twig', [
            'form' => $form->createView()
        ]);
    }


}
