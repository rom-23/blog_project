<?php

namespace App\EventListener;

use App\Entity\Development\Development;
use Doctrine\ORM\Event\LifecycleEventArgs;

class DevelopmentListener
{
    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();
        if (true === property_exists($entity, 'slug') && $entity instanceof Development) {
            $slug = strtolower(str_replace(' ', '-', $entity->getTitle()));
            $entity->setSlug($slug);
        }
    }

    public function postPersist(LifecycleEventArgs $args): void
    {
//        $entity = $args->getEntity();
//        if (true === property_exists($entity, 'slug') && $entity instanceof Development) {
//            $entity->setSlug('rrrrrr');
//        }
    }

    public function preUpdate(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();
        if (true === property_exists($entity, 'slug') && $entity instanceof Development) {
            $slug = strtolower(str_replace(' ', '-', $entity->getTitle()));
            $entity->setSlug($slug);
        }
    }
}
