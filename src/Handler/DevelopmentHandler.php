<?php

namespace App\Handler;

use App\Form\Development\DevelopmentAddType;
use App\Service\UploadInterface;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\UnitOfWork;
use Symfony\Component\HttpFoundation\Request;

class DevelopmentHandler extends AbstractHandler
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
        return DevelopmentAddType::class;
    }

    /**
     * @param object $data
     * @param Request $request
     */
    protected function process(object $data, Request $request): void
    {
        if ($this->em->getUnitOfWork()->getEntityState($data) === UnitOfWork::STATE_NEW) {
            $files = $this->form->get('files')->getData();
            if ($files) {
                $files = $request->files->get('development_add')['files'];
                $this->upload->uploadDevFile($files, $data);
            }
            $this->em->persist($data);
        } elseif (count($this->form->get('files')->getData()) > 0) {
            $files = $request->files->get('development_add')['files'];
            if ($files !== null) {
                $this->upload->uploadDevFile($files, $data);
                $data->setUpdatedAt(new DateTimeImmutable('now'));
            }
            $this->em->persist($data);
        }
        $this->em->flush();
    }

}
