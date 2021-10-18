<?php

namespace App\Controller\Security;

use App\Entity\User;
use App\Form\User\RegistrationFormType;
use App\Service\SendEmail;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class RegistrationController extends AbstractController
{

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, TokenGeneratorInterface $tokenGenerator, SendEmail $sendEmail): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $registrationToken = $tokenGenerator->generateToken();
            $user->setRegistrationToken($registrationToken);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            $sendEmail->send([
                'recipient_email' => $user->getEmail(),
                'subject'         => 'Verify your email to activate your account',
                'html_template'   => 'registration/register-confirmation-email.html.twig',
                'context'         => [
                    'userID'            => $user->getId(),
                    'registrationToken' => $registrationToken,
                    'tokenLifeTime'     =>  $user->getAccountMustBeVerifiedBefore()->format('d/m/Y at H:i')
                ]

            ]);
            $this->addFlash('success', 'Your account has been created, check your emails to activate it');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
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
        $this->addFlash('success','Your account has been activated, you can login');
        return $this->redirectToRoute('app_login');
    }

    private function isNotRequestedInTime(\DateTimeImmutable $accountMustBeVerifiedBefore): bool
    {
        return (new \DateTimeImmutable('now') > $accountMustBeVerifiedBefore);
    }
}
