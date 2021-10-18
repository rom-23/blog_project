<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{

    #[Route(path: '/apiplatform/login', name: 'api_platform_login', methods: ['POST'])]
    public function apiPlatformLogin(): JsonResponse
    {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->json([
                'error' => 'Invalid login request: check that the Content-Type header is "application/json".'
            ], 400);
        }
        if (null === $this->getUser()) {
            return $this->json([
                'message' => 'missing credentials',
            ], Response::HTTP_UNAUTHORIZED);
        }
        return $this->json([
                'user' => $this->getUser()->getUserIdentifier()
            ]
        );
    }

    #[Route(path: '/logout', name: 'logout', methods: ['POST'])]
    public function apiPlatformLogout()
    {

    }

    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        return $this->render('security/login.html.twig', [
            'error'         => $error
        ]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {

    }

    /**
     * @Route("/denied", name="access_denied")
     */
    public function denied(): Response
    {
        return $this->render('security/access_denied.html.twig');
    }
}
