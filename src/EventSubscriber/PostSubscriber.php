<?php

namespace App\EventSubscriber;

use App\Event\PostEvent;
use App\Service\SendEmail;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class PostSubscriber implements EventSubscriberInterface
{
    /**
     * @var SendEmail
     */
    protected SendEmail $sendEmail;

    /**
     * @param SendEmail $sendEmail
     */
    public function __construct(SendEmail $sendEmail)
    {
        $this->sendEmail = $sendEmail;
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function onSendPost(PostEvent $event)
    {
        $post       = $event->getPost();
        $parameters = [
            'recipient_email' => $post->getUser()->getEmail(),
            'subject'         => 'You have sent a new post',
            'html_template'   => 'symfony-app/post/new-post-email.html.twig',
            'context'         => [
                'userID' => $post->getUser()->getId(),
                'post'   => $post->getContent(),
                'dev'    => $post->getDevelopment()->getTitle()
            ]
        ];
        $this->sendEmail->send($parameters);
    }

    /**
     * @return \array[][]
     */
    #[ArrayShape([PostEvent::class => "array[]"])]
    public static function getSubscribedEvents(): array
    {
        return [
            PostEvent::class => [
                ['onSendPost', 1]
            ]
        ];
    }
}
