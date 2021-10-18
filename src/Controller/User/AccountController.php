<?php

namespace App\Controller\User;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Dompdf\Dompdf;
use Dompdf\Options;

class AccountController extends AbstractController
{
    #[Route(path: '/account', name: 'user_account')]
    public function home(): Response
    {
        return $this->render('account/account.html.twig');
    }

    #[Route(path: '/account/data', name: 'user_data')]
    public function getUserData(): Response
    {
        return $this->render('account/data.html.twig');
    }

    #[Route(path: '/account/download-data', name: 'download_user_data')]
    public function downloadUserData(): Response
    {
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->setIsRemoteEnabled(true);
        $dompdf = new Dompdf($pdfOptions);
        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => FALSE,
                'verify_peer_name' => FALSE,
                'allow_self_signed' => TRUE
            ]
        ]);
        $dompdf->setHttpContext($context);
        $html = $this->renderView('account/download-user-data.html.twig');
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $file = 'user-data-'. $this->getUser()->getId() .'.pdf';
        // On envoie le PDF au navigateur
        $dompdf->stream($file, [
            'Attachment' => true
        ]);

        return new Response();
    }
}
