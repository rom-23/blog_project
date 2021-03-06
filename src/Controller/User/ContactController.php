<?php

namespace App\Controller\User;

use App\Form\User\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    /**
     * @Route("/contact", name="contact")
     * @param Request $request
     * @return Response
     */
    public function createContact(Request $request): Response
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $this->AddFlash('success','Merci de nous avoir contacté. Notre équipe va vous répondre dans les meilleurs délais');
        }
        return $this->render('user/contact/contact.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
