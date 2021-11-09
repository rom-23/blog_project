<?php

namespace App\Controller\RootAdmin;

use App\Entity\Modelism\Image;
use App\Entity\Modelism\Model;
use App\Handler\ModelHandler;
use App\Repository\Modelism\ModelRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RootAdminModelController extends AbstractController
{
    #[Route('/root/admin/model/list', name: 'root_admin_model_list')]
    public function listModel(ModelRepository $modelRepository): Response
    {
        return $this->render('root-admin/model/model_list.html.twig', [
            'models' => $modelRepository->findAll()
        ]);
    }

    /**
     * @param ModelHandler $modelHandler
     * @param Request $request
     * @return Response
     */
    #[Route('/root/admin/model/add', name: 'root_admin_model_add', methods: ['GET', 'POST'])]
    public function addModel(ModelHandler $modelHandler, Request $request): Response
    {
        $model = new Model();
        if ($modelHandler->handle($request, $model,[])) {
            $this->addFlash('success', 'A new model has been created.');
            return $this->redirectToRoute('root_admin_model_list');
        }
        return $this->render('root-admin/model/model_add.html.twig', [
            'form' => $modelHandler->createView()
        ]);
    }

    #[Route('/root/admin/model/{id<\d+>}', name: 'root_admin_model_edit')]
    public function editModel(ModelHandler $modelHandler, Model $model, Request $request, EntityManagerInterface $em): Response
    {
        $options = ['validation_groups'=>['Default']];
        if ($modelHandler->handle($request, $model, $options)) {
            $this->addFlash('success', 'This model has been updated.');
            return $this->redirectToRoute('root_admin_model_list');
        }
        return $this->render('root-admin/model/model_edit.html.twig', [
            'model' => $model,
            'form'  => $modelHandler->createView()
        ]);
    }

    /**
     * @param EntityManagerInterface $em
     * @param Model $model
     * @return Response
     */
    #[Route('/root/admin/model/delete/{id<\d+>}', name: 'root_admin_model_delete')]
    public function deleteModel(EntityManagerInterface $em, Model $model): Response
    {
        $em->remove($model);
        $em->flush();
        $this->addFlash('success', 'The model has been deleted.');
        return $this->redirectToRoute('root_admin_model_list');
    }

    /**
     * @param Image $file
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    #[Route('/root/admin/model/delete/image/{id}', name: 'root_admin_model_delete_image', methods: ['DELETE'])]
    public function deleteModelAttachment(Image $image, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if ($this->isCsrfTokenValid('delete' . $image->getId(), $data['_token'])) {
            $fileName = $image->getName();
            unlink($this->getParameter('app.path.model_image') . '/' . $fileName);
            $image->getModels()->setUpdatedAt(new DateTimeImmutable('now'));
            $em->remove($image);
            $em->flush();
            return new JsonResponse(['success' => 1]);
        } else {

            return new JsonResponse(['error' => 'Invalid Token'], 400);
        }
    }
}
