<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class SendEmail
{
    private MailerInterface $mailer;
    private string $senderEmail;
    private string $senderName;

    /**
     * @param MailerInterface $mailer
     * @param string $senderEmail
     * @param string $senderName
     */
    public function __construct(MailerInterface $mailer, string $senderEmail, string $senderName)
    {
        $this->mailer      = $mailer;
        $this->senderEmail = $senderEmail;
        $this->senderName  = $senderName;
    }

    /**
     * @param array $arguments
     * @throws TransportExceptionInterface
     */
    public function send(array $arguments): void
    {
        [
            'recipient_email' => $recipientEmail,
            'subject'         => $subject,
            'html_template'   => $htmlTemplate,
            'context'         => $context,
        ] = $arguments;

        $email = new TemplatedEmail();
        $email->from(new Address($this->senderEmail, $this->senderName))
              ->to($recipientEmail)
              ->subject($subject)
              ->htmlTemplate($htmlTemplate)
              ->context($context);
        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $exception) {
            throw new TransportException($exception->getMessage());
        }
    }
}

