<?php

namespace App\EventListener;

use App\Entity\Development\Note;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;

class NoteEntityListener
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * @param Note $note
     * @param LifecycleEventArgs $args
     */
    public function prePersist(Note $note, LifecycleEventArgs $args): void
    {
        $note->setUser($this->security->getUser());
    }

    /**
     * @param Note $note
     * @param LifecycleEventArgs $args
     */
    public function preUpdate(Note $note, LifecycleEventArgs $args): void
    {

    }

}
