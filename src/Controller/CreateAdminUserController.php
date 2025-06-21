<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class CreateAdminUserController extends AbstractController
{
    #[Route('/create-admin', name: 'app_create_admin')]
    public function createAdmin(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        try {
            // Vérifier si un utilisateur existe déjà avec cette email
            $existingUser = $entityManager->getRepository(User::class)->findOneBy(['email' => 'symfony@example.com']);
            if ($existingUser) {
                $this->addFlash('error', 'Un utilisateur existe déjà avec cette email.');
                return $this->redirectToRoute('app_login');
            }

            // Créer un nouvel admin
            $admin = new User();
            $admin->setEmail('symfony@example.com');
            $admin->setPassword($passwordHasher->hashPassword($admin, 'admin123')); // Mot de passe par défaut
            $admin->setRoles(['ROLE_ADMIN']);
            $admin->setNom('Administrateur');
            $admin->setPrenom('Symfony');
            $admin->setTelephone('0612345678');
            $admin->setAdresse('123 Rue de Symfony');
            $admin->setRole('ROLE_ADMIN');

            // Sauvegarder l'utilisateur
            $entityManager->persist($admin);
            $entityManager->flush();

            $this->addFlash('success', 'Administrateur créé avec succès !');
            return $this->redirectToRoute('app_login');

        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de la création de l\'administrateur : ' . $e->getMessage());
            return $this->redirectToRoute('app_login');
        }
    }
}
