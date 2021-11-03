<?php

namespace App\Controller\RootAdmin;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Handler\UserHandler;
use App\Service\ManagePaginator;
use App\Service\UploadInterface;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;

class RootAdminUserController extends AbstractController
{
    /**
     * @param UserRepository $userRepository
     * @param Request $request
     * @param ManagePaginator $managePaginator
     * @return Response
     */
    #[Route('/root/admin/users', name: 'root_admin_users')]
    public function listUser(UserRepository $userRepository, Request $request, ManagePaginator $managePaginator): Response
    {
        $limit = $request->get('limit', 7);
        $page  = $request->get('page', 1);
        $users = $managePaginator->paginate($userRepository->findAllUsers(), $page, $limit);
        return $this->render('root-admin/user/user-list.html.twig', [
            'users' => $users,
            'pages'        => $managePaginator->lastPage($users),
            'page'         => $page,
            'limit'        => $limit,
            'range'        => $managePaginator->rangePaginator($page, $users)
        ]);
    }

    /**
     * @param User $user
     * @param Request $request
     * @param UserHandler $userHandler
     * @param EntityManagerInterface $em
     * @return Response
     */
    #[Route('/root/admin/user/{id<\d+>}', name: 'root_admin_user_edit', methods: ['GET', 'POST'])]
    public function editUser(User $user, Request $request, UserHandler $userHandler, EntityManagerInterface $em): Response
    {
        $options = ['validation_groups' => ['Default']];
        if ($userHandler->handle($request, $user, $options)) {
            $this->addFlash('success', 'User has been updated.');
            return $this->redirectToRoute('root_admin_users');
        }

        return $this->render('root-admin/user/user-view-edit.html.twig', [
            'user' => $user,
            'form' => $userHandler->createView()
        ]);
    }

    /**
     * @param Request $request
     * @param UserHandler $userHandler
     * @param EntityManagerInterface $em
     * @param UploadInterface $upload
     * @return Response
     */
    #[Route('/root/admin/user/add', name: 'root_admin_user_add', methods: ['GET', 'POST'])]
    public function addUser(Request $request, UserHandler $userHandler, EntityManagerInterface $em): Response
    {
        $user    = new User();
        $options = ['validation_groups' => ['create']];
        if ($userHandler->handle($request, $user, $options)) {
            $this->addFlash('success', 'A new user has been added good.');
            return $this->redirectToRoute('root_admin_users');
        }

        return $this->render('root-admin/user/user-add.html.twig', [
            'form' => $userHandler->createView()
        ]);
    }

    /**
     * @param EntityManagerInterface $em
     * @param User $user
     * @return Response
     */
    #[Route('/root/admin/user/delete/{id<\d+>}', name: 'root_admin_user_delete')]
    public function deleteUser(EntityManagerInterface $em, User $user): Response
    {
        $em->remove($user);
        $em->flush();
        $this->addFlash('success', 'The user has been deleted.');
        return $this->redirectToRoute('root_admin_users');
    }

    /**
     * @throws Exception
     */
    #[Route('/users/remove-inactive-account', name: 'remove_inactive_account')]
    public function removeInactiveAccount(KernelInterface $kernel): Response
    {
        $application = new Application($kernel);
        $application->setAutoExit(false);
        $input  = new ArrayInput([
            'command' => 'app:delete-inactive-accounts'
        ]);
        $output = new BufferedOutput();
        $application->run($input, $output);
        $content = $output->fetch();

        return new Response($content);
    }
}
