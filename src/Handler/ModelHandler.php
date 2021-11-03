<?php

namespace App\Handler;

use App\Form\Modelism\ModelAddType;
use App\Service\UploadInterface;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\UnitOfWork;
use Symfony\Component\HttpFoundation\Request;

class ModelHandler extends AbstractHandler
{

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $em;

    /**
     * @var UploadInterface
     */
    private UploadInterface $upload;

    /**
     * @param EntityManagerInterface $em
     * @param UploadInterface $upload
     */
    public function __construct(EntityManagerInterface $em, UploadInterface $upload)
    {
        $this->em     = $em;
        $this->upload = $upload;
    }

    /**
     * @return string
     */
    protected function getFormType(): string
    {
        return ModelAddType::class;
    }

    /**
     * @param object $data
     * @param Request $request
     */
    protected function process(object $data, Request $request): void
    {
        $file = $this->form->get('thumbnail')->getData();
        if($file !== null){
            $this->upload->uploadThumbnail($file, $data);
        }
        if ($this->em->getUnitOfWork()->getEntityState($data) === UnitOfWork::STATE_NEW) {
            $images = $this->form->get('images')->getData();
            if ($images) {
                $images = $request->files->get('model_add')['images'];
                $this->upload->uploadModelAttachments($images, $data);
            }
            $this->em->persist($data);
        } elseif (count($this->form->get('images')->getData()) > 0) {
            $images = $request->files->get('model_add')['images'];
            if ($images !== null) {
                $this->upload->uploadModelAttachments($images, $data);
                $data->setUpdatedAt(new DateTimeImmutable('now'));
            }
            $this->em->persist($data);
        }
        $this->em->flush();
    }

}
