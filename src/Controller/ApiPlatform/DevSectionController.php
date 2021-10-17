<?php

namespace App\Controller\ApiPlatform;

use App\Entity\Development\Development;
use App\Repository\Development\DevelopmentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
class DevSectionController extends AbstractController
{
    public function __construct(private DevelopmentRepository $developmentRepository)
    {

    }

    /**
     * @param string $id
     * @return array
     */
    #[Route('/developments/section/{id}', name: 'doc_by_section', methods: ['GET'])]
    public function __invoke(string $id): array
    {
        $documentation = $this->developmentRepository->findDocBySection($id);
        if (!$documentation) {
            throw $this->createNotFoundException(
                'No Documentation found for this section'
            );
        }
        return $documentation;
    }
}
