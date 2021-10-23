<?php

namespace App\Controller\RootAdmin;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;

class RootAdminUserController extends AbstractController
{
    #[Route('/root/admin/users', name: 'root_admin_users')]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('root-admin/user/user_list.html.twig', [
            'users' => $userRepository->findAll()
        ]);
    }

    #[Route('/root/admin/user/{id<\d+>}', name: 'root_admin_user_edit', methods: ['GET', 'POST'])]
    public function edit(User $user, EntityManagerInterface $em): Response
    {
        $user = $em->getRepository(User::class)->find($user);
        if (!$user) {
            $this->redirectToRoute('root_admin_user_edit');
        }
        return $this->render('root-admin/user/user_view.html.twig', [
            'user' => $user
        ]);
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
