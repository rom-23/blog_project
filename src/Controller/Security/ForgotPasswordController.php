<?php

namespace App\Controller\Security;

use App\Entity\User;
use App\Form\User\ForgotPasswordType;
use App\Form\User\ResetPasswordType;
use App\Repository\UserRepository;
use App\Service\SendEmail;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class ForgotPasswordController extends AbstractController
{
    private EntityManagerInterface $em;
    private SessionInterface $session;
    private UserRepository $userRepository;

    /**
     * @param EntityManagerInterface $em
     * @param SessionInterface $session
     * @param UserRepository $userRepository
     */
    public function __construct(EntityManagerInterface $em, SessionInterface $session, UserRepository $userRepository)
    {
        $this->em             = $em;
        $this->session        = $session;
        $this->userRepository = $userRepository;
    }

    #[Route('/forgot-password', name: 'forgot_password', methods: ['GET', 'POST'])]
    public function sendRecoveryLink(Request $request, SendEmail $sendEmail, TokenGeneratorInterface $tokenGenerator): Response
    {
        $form = $this->createForm(ForgotPasswordType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->userRepository->findOneBy([
                'email' => $form['email']->getData()
            ]);
            /* we make a lure */
            if (!$user) {
                $this->addFlash('success', 'An email has been sent to redefine your password');
                return $this->redirectToRoute('app_login');
            }
            $user->setForgotPasswordToken($tokenGenerator->generateToken());
            $user->setForgotPasswordTokenRequestedAt(new DateTimeImmutable('now'));
            $user->setForgotPasswordTokenMustBeVerifiedBefore(new DateTimeImmutable('+15 minutes'));
            $this->em->flush();
            $sendEmail->send([
                'recipient_email' => $user->getEmail(),
                'subject'         => 'Update your password',
                'html_template'   => 'security/forgot-password-email.html.twig',
                'context'         => [
                    'user' => $user
                ]
            ]);
            $this->addFlash('success', 'An email has been sent to redefine your password');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/forgot-password-step-1.html.twig', [
            'forgotPasswordFormStep1' => $form->createView()
        ]);
    }

    #[Route('/forgot-password/{id<\d+>}/{token}', name: 'retrieve_credentials', methods: ['GET'])]
    public function retrieveCredentialsFromTheURL(string $token, User $user): RedirectResponse
    {
        $this->session->set('Reset-Password-Token-URL', $token);
        $this->session->set('Reset-Password-User-Email', $user->getEmail());
        return $this->redirectToRoute('reset_password');
    }

    #[Route('/reset-password', name: 'reset_password', methods: ['GET', 'POST'])]
    public function resetPassword(Request $request, UserPasswordHasherInterface $passwordEncoder): RedirectResponse|Response
    {
        [
            'token'     => $token,
            'userEmail' => $userEmail
        ] = $this->getCredentialsFromSession();

        $user = $this->userRepository->findOneBy(['email' => $userEmail]);
        if (!$user) {
            return $this->redirectToRoute('forgot_password');
        }

        /** @var DateTimeImmutable $forgotPasswordTokenMustBeVerifiedBefore */
        $forgotPasswordTokenMustBeVerifiedBefore = $user->getForgotPasswordTokenMustBeVerifiedBefore();

        if (($user->getForgotPasswordToken() === null) || ($user->getForgotPasswordToken() !== $token) || ($this->isNotRequestedInTime($forgotPasswordTokenMustBeVerifiedBefore))) {
            return $this->redirectToRoute('forgot_password');
        }
        $form = $this->createForm(ResetPasswordType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($passwordEncoder->hashPassword($user, $form['password']->getData()));
            /* clear the token */
            $user->setForgotPasswordToken(null);
            $user->setForgotPasswordTokenVerifiedAt(new \DateTimeImmutable('now'));
            $this->em->flush();
            $this->removeCredentialsFromSession();
            $this->addFlash('success', 'Your password has been updated');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/forgot-password-step-2.html.twig', [
            'forgotPasswordFormStep2'      => $form->createView(),
            'passwordMustBeModifiedBefore' => $this->passwordMustBeModifiedBefore($user)
        ]);
    }

    /**
     * Gets the userID and token to the session
     * @return array
     */
    #[ArrayShape(['token' => "mixed", 'userEmail' => "mixed"])]
    private function getCredentialsFromSession(): array
    {
        return [
            'token'     => $this->session->get('Reset-Password-Token-URL'),
            'userEmail' => $this->session->get('Reset-Password-User-Email')
        ];
    }

    /**
     * Validates or not the fact that the link was clicked in the alloted time
     * @param DateTimeImmutable $forgotPasswordTokenMustBeVerifiedBefore
     * @return bool
     */
    private function isNotRequestedInTime(\DateTimeImmutable $forgotPasswordTokenMustBeVerifiedBefore): bool
    {
        return (new \DateTimeImmutable('now') > $forgotPasswordTokenMustBeVerifiedBefore);
    }

    /**
     * Remove the userID and token from the session
     * @return void
     */
    private function removeCredentialsFromSession(): void
    {
        $this->session->remove('Reset-Password-Token-URL');
        $this->session->remove('Reset-Password-User-Email');
    }

    /**
     * Returns the time before the password must be changed
     * @param User $user
     * @return string
     */
    private function passwordMustBeModifiedBefore(User $user): string
    {
        /** @var DateTimeImmutable $forgotPasswordTokenMustBeVerifiedBefore */
        $passwordMustBeModifiedBefore = $user->getForgotPasswordTokenMustBeVerifiedBefore();
        return $passwordMustBeModifiedBefore->format('H\hi');
    }
}
