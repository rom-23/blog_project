<?php

namespace App\Controller\ApiPlatform;

use App\Entity\Development\Development;
use Symfony\Component\HttpFoundation\Request;

class DevUploadController
{
    /**
     * @param Request $request
     */
    public function __invoke(Request $request)
    {
        $development = $request->attributes->get('data');
        if (!($development instanceof Development)) {
            throw new \RuntimeException('Error : Need development object');
        }
        $development->setFile($request->files->get('file'));
        $development->setUpdatedAt(new \DateTime());
//        dd($development);
        return $development;
    }
}
