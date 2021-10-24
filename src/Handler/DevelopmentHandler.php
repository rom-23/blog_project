<?php

namespace App\Handler;

use App\Form\Development\DevelopmentAddType;
use App\Service\ManageUploadFile;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\UnitOfWork;

class DevelopmentHandler extends AbstractHandler
{

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $em;

    /**
     * @var ManageUploadFile
     */
    private ManageUploadFile $manageUploadFile;

    /**
     * @param EntityManagerInterface $em
     * @param ManageUploadFile $manageUploadFile
     */
    public function __construct(EntityManagerInterface $em, ManageUploadFile $manageUploadFile)
    {
        $this->em               = $em;
        $this->manageUploadFile = $manageUploadFile;
    }

    /**
     * @return string
     */
    protected function getFormType(): string
    {
        return DevelopmentAddType::class;
    }

    /**
     * @param $data
     * @param $request
     */
    protected function process($data, $request): void
    {
        if ($this->em->getUnitOfWork()->getEntityState($data) === UnitOfWork::STATE_NEW) {
            $files = $this->form->get('files')->getData();
            if ($files !== null) {
                $files = $request->files->get('development_add')['files'];
                $this->manageUploadFile->uploadPdf($files, $data);
            }
            $this->em->persist($data);
        } elseif (count($this->form->get('files')->getData()) > 0) {
            $files = $request->files->get('development_add')['files'];
            if ($files !== null) {
                $this->manageUploadFile->uploadPdf($files, $data);
                $data->setUpdatedAt(new DateTimeImmutable('now'));
            }
            $this->em->persist($data);
        }
        $this->em->flush();
    }
}
