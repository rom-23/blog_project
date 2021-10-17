<?php

namespace App\Controller\RootAdmin;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RootAdminHomeController extends AbstractController
{
    #[Route('/root/admin/home', name: 'root_admin_home')]

    public function home(Request $request, EntityManagerInterface $em): Response
    {
        return $this->render('root-admin/home.html.twig');
    }
}
