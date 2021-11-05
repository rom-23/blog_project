<?php

namespace App\Controller\User;

use App\Entity\Address;
use App\Form\User\AddressType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
            return $this->redirectToRoute('user_account');
//            if ($cart->get()) {
//                return $this->redirectToRoute('order');
//            } else {
//                return $this->redirectToRoute('account_address');
//            }
        }
        return $this->render('_partials/_address.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @param Address $address
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     */
    #[Route('/account/address/edit/{id<\d+>}', name: 'account_address_edit', methods: ['GET', 'POST'])]
    public function editAddress(Address $address, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            if ($request->isXmlHttpRequest()) {
                return new Response(null, 204);
            }
            return $this->redirectToRoute('user_account');
        }

        return $this->render('_partials/_address.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @param Address $address
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    #[Route('/account/address/delete/{id<\d+>}', name: 'account_address_delete', methods: ['DELETE'])]
    public function deleteAddress(Address $address, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if ($this->isCsrfTokenValid('delete' . $address->getId(), $data['_token'])) {
            $em->remove($address);
            $em->flush();

            return new JsonResponse(['success' => 1]);
        } else {

            return new JsonResponse(['error' => 'Invalid Token'], 400);
        }
    }
}
