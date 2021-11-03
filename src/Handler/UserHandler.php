<?php

namespace App\Handler;

use App\Form\User\UserAddType;
use App\Service\UploadInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\UnitOfWork;
use Symfony\Component\HttpFoundation\Request;

class UserHandler extends AbstractHandler
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
     * @param UploadInterface $manageUploadFile
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
        return UserAddType::class;
    }

    /**
     * @param object $data
     * @param Request $request
     */
    protected function process(object $data, Request $request): void
    {
        $file = $this->form->get('imageFile')->getData();
        if($file !== null){
            $this->upload->upload($file, $data);
        }
        if ($this->em->getUnitOfWork()->getEntityState($data) === UnitOfWork::STATE_NEW) {
            $this->em->persist($data);
        }
        $this->em->flush();
    }

}
