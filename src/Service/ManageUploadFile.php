<?php

namespace App\Service;

use App\Entity\Development\Development;
use App\Entity\Development\DevelopmentFile;
use Gedmo\Sluggable\Util\Urlizer;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class ManageUploadFile
{
    /**
     * @var
     */
    private mixed $targetPdfDirectory;

    public function __construct($targetPdfDirectory)
    {
        $this->targetPdfDirectory = $targetPdfDirectory;
    }

    /**
     * @param array $files
     * @param Development $development
     */
    public function uploadPdf(array $files, Development $development)
    {
        foreach ($files as $file) {
            $originalFilename = pathinfo($file['name']->getClientOriginalName(), PATHINFO_FILENAME);
            $tempFile         = Urlizer::urlize($originalFilename) . '-' . uniqid() . '.' . $file['name']->guessExtension();
            try {
                $file['name']->move($this->getTargetPdfDirectory(), $tempFile);
            } catch (FileException $e) {
                throw new FileException($e->getMessage());
            }
            $fileToSave = new DevelopmentFile();
            $fileToSave->setName($tempFile);
            $fileToSave->setPath($this->getTargetPdfDirectory());
            $development->addFile($fileToSave);
        }
    }

    /**
     * @return mixed
     */
    public function getTargetPdfDirectory(): mixed
    {
        return $this->targetPdfDirectory;
    }

}
