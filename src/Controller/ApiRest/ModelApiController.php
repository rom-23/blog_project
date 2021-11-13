<?php

namespace App\Controller\ApiRest;

use App\Entity\Modelism\Model;
use App\Repository\Modelism\ModelRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class ModelApiController extends AbstractController
{
    /**
     * @param ModelRepository $modelRepository
     * @param $id
     * @return Response
     */
    #[Route('/rest-api/models/category/{id}', name: 'rest_api_models_by_category', methods: 'GET')]
    public function getModelsByCategory(ModelRepository $modelRepository, $id): Response
    {
        $models = $modelRepository->findModelsByCategory($id);
        return $this->render('symfony-app/model/models.html.twig', [
            'models'=>$models
        ]);
    }

    /**
     * @param ModelRepository $modelRepository
     * @return Response
     */
    #[Route('/rest-api/all-models', name: 'rest_api_all_models', methods: 'GET')]
    public function getAllModels(ModelRepository $modelRepository): Response
    {
        $models = $modelRepository->findAllModels();
        return $this->render('symfony-app/model/models.html.twig', [
            'models'=>$models
        ]);
//        return $this->json($models, 200, [], ['groups' => 'model:get']);
    }

    /**
     * @param Model $model
     * @return JsonResponse
     */
    #[Route('/rest-api/model/{id}', name: 'rest_api_get_model', methods: 'GET')]
    public function getModelById(Model $model): JsonResponse
    {
        return $this->json($model, 200, [], ['groups' => 'model:get']);
    }

    /**
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $em
     * @param UrlGeneratorInterface $urlGenerator
     * @return JsonResponse
     */
    #[Route('/rest-api/model/add', name: 'rest_api_add_model', methods: 'POST')]
    public function addModel(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, UrlGeneratorInterface $urlGenerator): JsonResponse
    {
        $json = json_encode(json_decode($request->getContent())->params);
        $model = $serializer->deserialize($json, Model::class, 'json');
        $em->persist($model);
        $em->flush();
        return $this->json($model, 201, ['location' => $urlGenerator->generate('rest_api_get_model', ['id' => $model->getId()])]);
    }

    /**
     * @param Model $model
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    #[Route('/rest-api/model/edit/{id}', name: 'rest_api_edit_model', methods: 'PATCH')]
    public function editModel(Model $model, Request $request, SerializerInterface $serializer, EntityManagerInterface $em): JsonResponse
    {
        $json = json_encode(json_decode($request->getContent())->params);
        $model = $serializer->deserialize($json, Model::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $model]);
        $em->flush();
        return $this->json($model, 200, [Response::HTTP_NO_CONTENT], ['groups' => 'model:get']);
    }

    /**
     * @param Model $model
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    #[Route('/rest-api/model/delete/{id}', name: 'rest_api_delete_model', methods: 'DELETE')]
    public function deleteModel(Model $model, EntityManagerInterface $em): JsonResponse
    {
//        $model = $em->getRepository(Model::class)->find($model);
        $em->remove($model);
        $em->flush();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

}
