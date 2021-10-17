<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Repository\Development\TagRepository;
use App\Repository\Modelism\ModelRepository;
use App\Repository\UserRepository;
use Doctrine\DBAL\Exception;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class ApiController extends AbstractController
{
    /**
     * @param UserRepository $userRepository
     * @return JsonResponse
     */
    #[Route('/api/all-users', name: 'api_all_users', methods: 'GET')]
    public function getAllUsers(UserRepository $userRepository): JsonResponse
    {
        $users = $userRepository->findAll(['id' => 'DESC']);
        return $this->json($users, 200, [], ['groups' => 'user:get']);
    }

    /**
     * @param User $user
     * @return JsonResponse
     */
    #[Route('/api/user/{id}', name: 'api_get_user', methods: 'GET')]
    public function getUserById(User $user): JsonResponse
    {
        return $this->json($user, 200, [], ['groups' => 'user:get']);
    }

    /**
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $em
     * @param UserPasswordHasherInterface $passwordHasher
     * @param UrlGeneratorInterface $urlGenerator
     * @return JsonResponse
     */
    #[Route('/api/users/add', name: 'api_add_user', methods: 'POST')]
    public function addUser(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher, UrlGeneratorInterface $urlGenerator): JsonResponse
    {
        $json = json_encode(json_decode($request->getContent())->params);
        $user = $serializer->deserialize($json, User::class, 'json');
        $user->setPassword($passwordHasher->hashPassword($user, $user->getPassword()));
        $em->persist($user);
        $em->flush();
        return $this->json($user, 201, ['location' => $urlGenerator->generate('api_get_user', ['id' => $user->getId()])]);
    }

    /**
     * @param User $user
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $em
     * @param UserPasswordHasherInterface $passwordHasher
     * @return JsonResponse
     */
    #[Route('/api/users/edit/{id}', name: 'api_add_user_edit', methods: 'PATCH')]
    public function editUser(User $user, Request $request, SerializerInterface $serializer, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $json = json_encode(json_decode($request->getContent())->params);
        $user = $serializer->deserialize($json, User::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $user]);
        $em->flush();
        return $this->json($user, 200, [Response::HTTP_NO_CONTENT], ['groups' => 'user:get']);
    }

    /**
     * @param User $user
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    #[Route('/api/users/remove/{id}', name: 'api_add_user_remove', methods: 'DELETE')]
    public function removeUser(User $user, EntityManagerInterface $em): JsonResponse
    {
        $user = $em->getRepository(User::class)->find($user);
        $em->remove($user);
        $em->flush();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @param ModelRepository $modelRepository
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    #[Route('/api/all-models', name: 'api_all_models', methods: 'GET')]
    public function getAllModels(ModelRepository $modelRepository, SerializerInterface $serializer): JsonResponse
    {
        $models = $modelRepository->findModelPng();
        return $this->json($models, 200, [], ['groups' => 'get']);
    }

    #[Route('/api/development/tags', name: 'api_tag_development')]
    public function index(Request $request, TagRepository $tagRepository): JsonResponse
    {
        return $this->json($tagRepository->search($request->query->get('q')));
    }

    /**
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    #[Route('/api/development/add', name: 'api_add_development', methods: 'POST')]
    public function addDevelopment(Request $request, SerializerInterface $serializer, EntityManagerInterface $em): JsonResponse
    {
        dd(json_decode($request->getContent())->params);
//        $json = dd(json_encode(json_decode($request->getContent())->params));
//        $user = $serializer->deserialize($json, User::class, 'json');
//        $em->persist($user);
//        $em->flush();
//        return $this->json($user, 201, []);
    }
}
