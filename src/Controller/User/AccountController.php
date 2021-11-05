<?php

namespace App\Controller\User;

use App\Entity\User;
use App\Form\User\ChangePasswordType;
use App\Handler\UserHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
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

    /**
     * @param User $user
     * @param Request $request
     * @param UserHandler $userHandler
     * @return Response
     */
    #[Route('/account/edit/{id<\d+>}', name: 'user_account_edit', methods: ['GET', 'POST'])]
    public function editAccount(User $user, Request $request, UserHandler $userHandler): Response
    {
        $options = ['validation_groups' => ['Default']];
        if ($userHandler->handle($request, $user, $options)) {
            $this->addFlash('success', 'Your account has been updated.');
            return $this->redirectToRoute('user_account');
        }

        return $this->render('account/account-edit.html.twig', [
            'user' => $user,
            'form' => $userHandler->createView()
        ]);
    }

    /**
     * @param EntityManagerInterface $em
     * @param User $user
     * @return Response
     */
    #[Route('/account/delete/{id<\d+>}', name: 'user_account_delete')]
    public function deleteAccount(EntityManagerInterface $em, User $user): Response
    {
        $em->remove($user);
        $em->flush();
        $this->addFlash('success', 'Your account has been deleted.');
        return $this->redirectToRoute('home');
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param UserPasswordHasherInterface $encoder
     * @return Response
     */
    #[Route(path: '/account/change-password', name: 'change_account_password')]
    public function changePassword(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $encoder): Response
    {
        $user         = $this->getUser();
        $form         = $this->createForm(ChangePasswordType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $old_password = $form->get('old_password')->getData();
            if ($encoder->isPasswordValid($user, $old_password)) {
                $new_password = $form->get('new_password')->getData();
                $user->setPassword($new_password);
                $em->flush();
                if ($request->isXmlHttpRequest()) {
                    return new Response(null, 204);
                }
            } else {
                return new Response('Your original password is wrong.', 400);
            }
        }

        return $this->render('_partials/_password.html.twig', [
            'form' => $form->createView()
        ], new Response(
            null,
            $form->isSubmitted() && !$form->isValid() ? 422 : 200,
        ));
    }

    /**
     * @return Response
     */
    #[Route(path: '/account/data', name: 'user_data')]
    public function getUserData(): Response
    {
        return $this->render('account/data.html.twig');
    }

    /**
     * @return Response
     */
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
