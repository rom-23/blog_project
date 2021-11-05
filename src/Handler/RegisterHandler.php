<?php

namespace App\Handler;

use App\Form\User\RegistrationFormType;
use App\Form\User\UserAddType;
use App\Service\SendEmail;
use App\Service\UploadInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\UnitOfWork;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;


class RegisterHandler extends AbstractHandler
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $em;

    /**
     * @var TokenGeneratorInterface
     */
    private TokenGeneratorInterface $tokenGenerator;
    /**
     * @var UploadInterface
     */
    private UploadInterface $upload;

    /**
     * @var SendEmail
     */
    private SendEmail $sendEmail;

    /**
     * @param EntityManagerInterface $em
     * @param TokenGeneratorInterface $tokenGenerator
     * @param UploadInterface $upload
     * @param SendEmail $sendEmail
     */
    public function __construct(EntityManagerInterface $em, TokenGeneratorInterface $tokenGenerator, UploadInterface $upload, SendEmail $sendEmail)
    {
        $this->em             = $em;
        $this->tokenGenerator = $tokenGenerator;
        $this->upload         = $upload;
        $this->sendEmail      = $sendEmail;
    }


    /**
     * @return string
     */
    protected function getFormType(): string
    {
        return RegistrationFormType::class;
    }

    /**
     * @param object $data
     * @param Request $request
     * @throws TransportExceptionInterface
     */
    protected function process(object $data, Request $request): void
    {
        $file = $this->form->get('imageFile')->getData();
        if ($file !== null) {
            $this->upload->upload($file, $data);
        }
        $registrationToken = $this->tokenGenerator->generateToken();
        $data->setRegistrationToken($registrationToken);
        $this->em->persist($data);
        $this->em->flush();
        $this->sendEmail->send([
            'recipient_email' => $data->getEmail(),
            'subject'         => 'Verify your email to activate your account',
            'html_template'   => 'user/registration/register-confirmation-email.html.twig',
            'context'         => [
                'userID'            => $data->getId(),
                'registrationToken' => $registrationToken,
                'tokenLifeTime'     => $data->getAccountMustBeVerifiedBefore()->format('d/m/Y at H:i')
            ]
        ]);
    }

}
