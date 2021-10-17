<?php

namespace App\Controller\RootAdmin;

use App\Entity\Modelism\Model;
use App\Form\Modelism\ModelAddType;
use App\Repository\Modelism\ModelRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RootAdminModelController extends AbstractController
{
    #[Route('/root/admin/model/list', name: 'root_admin_model_list')]
    public function index(ModelRepository $modelRepository): Response
    {
        return $this->render('root-admin/model/model_list.html.twig', [
            'models' => $modelRepository->findAll()
        ]);
    }

    #[Route('/root/admin/model/add', name: 'root_admin_model_add')]
    public function modelAdd(Request $request, EntityManagerInterface $em): Response
    {
        $model = new Model();
        $form  = $this->createForm(ModelAddType::class, $model);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($form->getData()->getImages() as $image) {
                $em->persist($image);
                $model->addImage($image);
            }
            $em->persist($model);
            $em->flush();
            return $this->redirectToRoute('root_admin_home');
        }
        return $this->render('root-admin/model/model_add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/root/admin/model/{id<\d+>}', name: 'root_admin_model_edit')]
    public function edit(Model $model, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ModelAddType::class, $model);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            return $this->redirectToRoute('root_admin_model_list');
        }
        return $this->render('root-admin/model/model_add.html.twig', [
            'model'  => $model,
            'form' => $form->createView()
        ]);
    }

}
