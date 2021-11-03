<?php

namespace App\Controller\User;

use App\Form\User\ChangePasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AccountPasswordController extends AbstractController
{
    /**
     * @Route("/account/change-password", name="change_account_password")
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param UserPasswordHasherInterface $encoder
     * @return Response
     */
    public function changePassword(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $encoder): Response
    {
        $notification = null;
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

        return $this->render('account/password.html.twig', [
            'form'         => $form->createView(),
            'notification' => $notification
        ], new Response(
            null,
            $form->isSubmitted() && !$form->isValid() ? 422 : 200,
        ));
    }
}
