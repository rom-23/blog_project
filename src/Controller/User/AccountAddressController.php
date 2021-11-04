<?php

namespace App\Controller\User;

use App\Entity\Address;
use App\Form\User\AddressType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class AccountAddressController extends AbstractController
{

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $em;

    /**
     * AccountAddressController constructor.
     * @param $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/account/address", name="account_address")
     */
    public function index(): Response
    {
        return $this->render('account/address.html.twig', [
        ]);
    }

    /**
     * @Route("/account/add-address", name="account_address_add")
     * @param Request $request
     * @return Response
     */
    public function addressAdd(Request $request): Response
    {
        $address = new Address();
        $form   = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $address->setUser($this->getUser());
            $this->em->persist($address);
            $this->em->flush();
            return $this->redirectToRoute('account_address');
//            if ($cart->get()) {
//                return $this->redirectToRoute('order');
//            } else {
//                return $this->redirectToRoute('account_address');
//            }
        }
            return $this->render('account/address_form.html.twig', [
                'form' => $form->createView()
            ]);
        }

        /**
         * @Route("/account/edit-address/{id}", name="account_address_edit")
         * @param Request $request
         * @param $id
         * @return Response
         */
        public
        function addressEdit(Request $request, $id): Response
        {
            $address = $this->em->getRepository(Address::class)->find($id);
            if (!$address || $address->getUser() != $this->getUser()) {
                return $this->redirectToRoute('account_address');
            }
            $form = $this->createForm(AddressType::class, $address);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $this->em->flush();

                return $this->redirectToRoute('account_address');
            }

            return $this->render('account/address_form.html.twig', [
                'form' => $form->createView()
            ]);
        }

    /**
     * @Route("/account/delete-address/{id}", name="account_address_delete")
     * @param Request $request
     * @param $id
     * @return Response
     */
        public function addressDelete(Request $request, $id): Response
        {
            $address = $this->em->getRepository(Address::class)->find($id);
            if ($address && $address->getUser() == $this->getUser()) {
                $this->em->remove($address);
                $this->em->flush();
            }

            return $this->redirect($request->headers->get('referer'));
//            return $this->redirectToRoute('account_address');
        }
    }
