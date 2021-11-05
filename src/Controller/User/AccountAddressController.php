<?php

namespace App\Controller\User;

use App\Entity\Address;
use App\Form\User\AddressType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
     * @return Response
     */
    #[Route('/account/address', name: 'account_address')]
    public function index(): Response
    {
        return $this->render('account/address.html.twig', [
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     */
    #[Route('/account/add-address', name: 'account_address_add')]
    public function addressAdd(Request $request): Response
    {
        $address = new Address();
        $form    = $this->createForm(AddressType::class, $address);
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
     * @param Request $request
     * @param $id
     * @return Response
     */
    #[Route('/account/edit-address/{id<\d+>}', name: 'account_address_edit')]
    public function addressEdit(Request $request, $id): Response
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
     * @param Request $request
     * @param $id
     * @return Response
     */
    #[Route('/account/delete-address/{id<\d+>}', name: 'account_address_delete')]
    public function addressDelete(Request $request, $id): Response
    {
        $address = $this->em->getRepository(Address::class)->find($id);
        if ($address && $address->getUser() == $this->getUser()) {
            $this->em->remove($address);
            $this->em->flush();
        }

        return $this->redirect($request->headers->get('referer'));
    }
}
