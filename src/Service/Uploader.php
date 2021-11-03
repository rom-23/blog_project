<?php

namespace App\Service;

use App\Entity\Development\Development;
use App\Entity\Development\DevelopmentFile;
use App\Entity\Modelism\Image;
use App\Entity\Modelism\Model;
use App\Entity\User;
use Gedmo\Sluggable\Util\Urlizer;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use function PHPUnit\Framework\at;
use function PHPUnit\Framework\isInstanceOf;

class Uploader implements UploadInterface
{
    /**
     * @var
     */
    private mixed $targetPdfDirectory;

    /**
     * @var mixed
     */
    private mixed $targetModelThumbnailDirectory;

    /**
     * @var
     */
    private mixed $targetModelImageDirectory;

    /**
     * @var
     */
    private mixed $targetUserImageDirectory;

    /**
     * @param mixed $targetPdfDirectory
     * @param mixed $targetModelThumbnailDirectory
     * @param mixed $targetModelImageDirectory
     * @param mixed $targetUserImageDirectory
     */
    public function __construct(mixed $targetPdfDirectory, mixed $targetModelThumbnailDirectory, mixed $targetModelImageDirectory, mixed $targetUserImageDirectory)
    {
        $this->targetPdfDirectory            = $targetPdfDirectory;
        $this->targetModelThumbnailDirectory = $targetModelThumbnailDirectory;
        $this->targetModelImageDirectory     = $targetModelImageDirectory;
        $this->targetUserImageDirectory      = $targetUserImageDirectory;
    }

    /**
     * @param UploadedFile $uploadedFile
     * @param object $data
     */
    public function upload(UploadedFile $uploadedFile, object $data): void
    {
        $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
        $tempFile         = Urlizer::urlize($originalFilename) . '-' . uniqid() . '.' . $uploadedFile->guessExtension();
        try {
            $uploadedFile->move($this->getTargetUserImageDirectory(), $tempFile);
            $data->setImage($tempFile);
        } catch (FileException $e) {
            throw new FileException($e->getMessage());
        }
    }

    /**
     * @param UploadedFile $uploadedFile
     * @param object $data
     */
    public function uploadThumbnail(UploadedFile $uploadedFile, object $data): void
    {
        $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
        $tempFile         = Urlizer::urlize($originalFilename) . '-' . uniqid() . '.' . $uploadedFile->guessExtension();
        try {
            $uploadedFile->move($this->getTargetModelThumbnailDirectory(), $tempFile);
            $data->setThumbnail($tempFile);
        } catch (FileException $e) {
            throw new FileException($e->getMessage());
        }
    }

    /**
     * @param array $files
     * @param object $data
     */
    public function uploadDevFile(array $files, object $data): void
    {
        foreach ($files as $uploadedFile) {
            $originalFilename = pathinfo($uploadedFile['name']->getClientOriginalName(), PATHINFO_FILENAME);
            $tempFile         = Urlizer::urlize($originalFilename) . '-' . uniqid() . '.' . $uploadedFile['name']->guessExtension();
            try {
                $uploadedFile['name']->move($this->getTargetPdfDirectory(), $tempFile);
            } catch (FileException $e) {
                throw new FileException($e->getMessage());
            }
            $fileToSave = new DevelopmentFile();
            $fileToSave->setName($tempFile);
            $fileToSave->setPath($this->getTargetPdfDirectory());
            $data->addFile($fileToSave);
        }
    }

    /**
     * @param array $files
     * @param object $data
     */
    public function uploadModelAttachments(array $files, object $data): void
    {
        foreach ($files as $uploadedFile) {
            $originalFilename = pathinfo($uploadedFile['name']->getClientOriginalName(), PATHINFO_FILENAME);
            $tempFile         = Urlizer::urlize($originalFilename) . '-' . uniqid() . '.' . $uploadedFile['name']->guessExtension();
            try {
                $uploadedFile['name']->move($this->getTargetModelImageDirectory(), $tempFile);
            } catch (FileException $e) {
                throw new FileException($e->getMessage());
            }
            $fileToSave = new Image();
            $fileToSave->setName($tempFile);
            $data->addImage($fileToSave);
        }
    }

    /**
     * @return string
     */
    public function getTargetPdfDirectory(): string
    {
        return $this->targetPdfDirectory;
    }

    /**
     * @return string
     */
    public function getTargetModelThumbnailDirectory(): string
    {
        return $this->targetModelThumbnailDirectory;
    }

    /**
     * @return string
     */
    public function getTargetUserImageDirectory(): string
    {
        return $this->targetUserImageDirectory;
    }

    /**
     * @return string
     */
    public function getTargetModelImageDirectory(): string
    {
        return $this->targetModelImageDirectory;
    }
}
