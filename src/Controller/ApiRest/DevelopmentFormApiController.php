<?php

namespace App\Controller\ApiRest;

use App\Repository\Development\DevelopmentRepository;
use App\Repository\Development\TagRepository;
use App\Repository\Modelism\CategoryRepository;
use App\Repository\Modelism\OptionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DevelopmentFormApiController extends AbstractController
{

    /**
     * @param Request $request
     * @param TagRepository $tagRepository
     * @return JsonResponse
     */
    #[Route('/api/development/tags', name: 'api_tag_development')]
    public function searchDevelopmentTags(Request $request, TagRepository $tagRepository): JsonResponse
    {
        return $this->json($tagRepository->search($request->query->get('q')));
    }

    /**
     * @param Request $request
     * @param OptionRepository $optionRepository
     * @return JsonResponse
     */
    #[Route('/api/model/options', name: 'api_option_model')]
    public function searchModelOptions(Request $request, OptionRepository $optionRepository): JsonResponse
    {
        return $this->json($optionRepository->search($request->query->get('q')), 200, [], ['groups' => 'get']);
    }

    /**
     * @param Request $request
     * @param CategoryRepository $categoryRepository
     * @return JsonResponse
     */
    #[Route('/api/model/categories', name: 'api_category_model')]
    public function searchModelCategories(Request $request, CategoryRepository $categoryRepository): JsonResponse
    {
        return $this->json($categoryRepository->search($request->query->get('q')), 200, [], ['groups' => 'get']);
    }

}
