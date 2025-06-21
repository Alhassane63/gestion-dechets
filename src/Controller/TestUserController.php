<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class TestUserController extends AbstractController
{
    #[Route('/test-users', name: 'app_test_users')]
    public function testUsers(UserRepository $userRepository)
    {
        $users = $userRepository->findAll();
        
        foreach ($users as $user) {
            dump($user->getEmail());
            dump($user->getRoles());
            dump($user->getPassword());
        }
        
        return $this->redirectToRoute('app_login');
    }
}
