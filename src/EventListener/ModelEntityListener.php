<?php

namespace App\EventListener;

use App\Entity\Modelism\Model;
use Symfony\Component\HttpKernel\KernelInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Filesystem\Filesystem;

class ModelEntityListener
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
     * @param Model $model
     * @param LifecycleEventArgs $args
     */
    public function preRemove(Model $model, LifecycleEventArgs $args): void
    {
        $filesystem = new Filesystem();
        foreach ($model->getImages() as $file) {
            $filesystem->remove(['', $this->kernel->getProjectDir() . '/public/uploads/models/modelsAttachments/' . $file->getName(), '']);
        }
    }
}
