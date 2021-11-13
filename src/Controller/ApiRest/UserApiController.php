<?php

namespace App\Controller\ApiRest;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class UserApiController extends AbstractController
{
    /**
     * @param UserRepository $userRepository
     * @return JsonResponse
     */
    #[Route('/rest-api/all-users', name: 'rest_api_all_users', methods: 'GET')]
    public function getAllUsers(UserRepository $userRepository): JsonResponse
    {
        $users = $userRepository->findAll();
        return $this->json($users, 200, [], ['groups' => 'user:read']);
    }

    /**
     * @param User $user
     * @return JsonResponse
     */
    #[Route('/rest-api/user/{id}', name: 'rest_api_user_by_id', methods: 'GET')]
    public function getUserById(User $user): JsonResponse
    {
        return $this->json($user, 200, [], ['groups' => 'user:read']);
    }

    /**
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $em
     * @param UserPasswordHasherInterface $passwordHasher
     * @param UrlGeneratorInterface $urlGenerator
     * @return JsonResponse
     */
    #[Route('/rest-api/user/add', name: 'rest_api_add_user', methods: 'POST')]
    public function addUser(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher, UrlGeneratorInterface $urlGenerator): JsonResponse
    {
        $json = json_encode(json_decode($request->getContent())->params);
        $user = $serializer->deserialize($json, User::class, 'json');
        $user->setPassword($passwordHasher->hashPassword($user, $user->getPassword()));
        $em->persist($user);
        $em->flush();
        return $this->json($user, 201, ['location' => $urlGenerator->generate('rest_api_user_by_id', ['id' => $user->getId()])]);
    }

    /**
     * @param User $user
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $em
     * @param UserPasswordHasherInterface $passwordHasher
     * @return JsonResponse
     */
    #[Route('/rest-api/user/edit/{id}', name: 'rest_api_edit_user', methods: 'PATCH')]
    public function editUser(User $user, Request $request, SerializerInterface $serializer, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $json = json_encode(json_decode($request->getContent())->params);
        $user = $serializer->deserialize($json, User::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $user]);
        $em->flush();
        return $this->json($user, 200, [Response::HTTP_NO_CONTENT], ['groups' => 'user:read']);
    }

    /**
     * @param User $user
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    #[Route('/rest-api/user/delete/{id}', name: 'rest_api_delete_user', methods: 'DELETE')]
    public function removeUser(User $user, EntityManagerInterface $em): JsonResponse
    {
        $user = $em->getRepository(User::class)->find($user);
        $em->remove($user);
        $em->flush();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

}
