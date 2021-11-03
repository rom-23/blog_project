<?php

namespace App\EventListener;

use App\Entity\Development\Development;
use Symfony\Component\HttpKernel\KernelInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Filesystem\Filesystem;

class DevelopmentEntityListener
{
    /**
     * @var KernelInterface
     */
    private KernelInterface $kernel;

    /**
     * @param KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @param Development $development
     * @param LifecycleEventArgs $args
     */
    public function prePersist(Development $development, LifecycleEventArgs $args): void
    {
        if (true === property_exists($development, 'slug')) {
            $slug = strtolower(str_replace(' ', '-', $development->getTitle()));
            $development->setSlug($slug);
        }
    }

    /**
     * @param Development $development
     * @param LifecycleEventArgs $args
     */
    public function preUpdate(Development $development, LifecycleEventArgs $args): void
    {
        $userChanges = $args->getEntityChangeSet();
        if (array_key_exists('title', $userChanges)) {
            $slug = strtolower(str_replace(' ', '-', $userChanges['title'][1]));
            $development->setSlug($slug);
        }
    }

    /**
     * @param Development $development
     * @param LifecycleEventArgs $args
     */
    public function preRemove(Development $development, LifecycleEventArgs $args): void
    {
        $filesystem = new Filesystem();
        foreach ($development->getFiles() as $file) {
            $filesystem->remove(['', $this->kernel->getProjectDir() . '/public/uploads/dev-files/' . $file->getName(), '']);
        }
    }
}
