<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

interface UploadInterface
{
    /**
     * @param UploadedFile $uploadedFile
     * @param object $data
     */
    public function upload(UploadedFile $uploadedFile, object $data): void;

    /**
     * @param array $file
     * @param object $data
     */
    public function uploadDevFile(array $files, object $data): void;

    /**
     * @param UploadedFile $uploadedFile
     * @param object $data
     */
    public function uploadThumbnail(UploadedFile $uploadedFile, object $data): void;

    /**
     * @param array $files
     * @param object $data
     */
    public function uploadModelAttachments(array $files, object $data): void;
}
