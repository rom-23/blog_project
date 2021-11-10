<?php

namespace App\Controller\Security;

use App\Entity\User;
use App\Handler\RegisterHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    /**
     * @param RegisterHandler $registerHandler
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     */
    #[Route('/register', name: 'app_register')]
    public function register(RegisterHandler $registerHandler, Request $request, EntityManagerInterface $em): Response
    {
        $user    = new User();
        $options = ['validation_groups' => ['create']];
        if ($registerHandler->handle($request, $user, $options)) {
            $this->addFlash('success', 'Your account has been created, check your emails to activate it');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('symfony-app/user/registration/register.html.twig', [
            'registrationForm' => $registerHandler->createView(),
        ]);
    }

    #[Route('/{id<\d+>}/{token}', name: 'app_verify_account', methods: ['GET'])]
    public function verifyUserAccount(User $user, EntityManagerInterface $em, string $token): Response
    {
        if (($user->getRegistrationToken() === null) || ($user->getRegistrationToken() !== $token)
            || ($this->isNotRequestedInTime($user->getAccountMustBeVerifiedBefore()))) {
            throw new AccessDeniedException();
        }
        $user->setIsVerified(true);
        $user->setAccountVerifiedAt(new \DateTimeImmutable('now'));
        $user->setRegistrationToken(null);
        $em->flush();
        $this->addFlash('success', 'Your account has been activated, you can login');
        return $this->redirectToRoute('app_login');
    }

    private function isNotRequestedInTime(\DateTimeImmutable $accountMustBeVerifiedBefore): bool
    {
        return (new \DateTimeImmutable('now') > $accountMustBeVerifiedBefore);
    }
}
