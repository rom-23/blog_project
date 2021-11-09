<?php

namespace App\Controller\RootAdmin;

use App\Entity\Modelism\Opinion;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RootAdminOpinionController extends AbstractController
{
    /**
     * @param Opinion $opinion
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    #[Route('/root/admin/model/opinion/delete/{id<\d+>}', name: 'root_admin_opinion_delete', methods: ['DELETE'])]
    public function deleteOpinion(Opinion $opinion, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if ($this->isCsrfTokenValid('delete' . $opinion->getId(), $data['_token'])) {
            $opinion->getModel()->setUpdatedAt(new DateTimeImmutable('now'));
            $em->remove($opinion);
            $em->flush();

            return new JsonResponse(['success' => 1]);
        } else {

            return new JsonResponse(['error' => 'Invalid Token'], 400);
        }
    }
}
