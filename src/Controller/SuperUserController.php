<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class SuperUserController extends AbstractController
{
    #[Route('/superuser/create', name: 'app_superuser_create')]
    public function createSuperuser(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        try {
            // Vérifier si un utilisateur existe déjà
            $existingUser = $entityManager->getRepository(User::class)->findOneBy([]);
            if ($existingUser) {
                $this->addFlash('error', 'Un utilisateur existe déjà dans la base de données.');
                return new RedirectResponse($this->generateUrl('app_login'));
            }

            // Créer un nouveau superuser
            $superuser = new User();
            $superuser->setEmail('admin@example.com');
            $superuser->setPassword($passwordHasher->hashPassword($superuser, 'admin123'));
            $superuser->setRoles(['ROLE_SUPER_ADMIN']);
            $superuser->setNom('Administrateur');
            $superuser->setPrenom('Système');
            $superuser->setTelephone('0612345678');
            $superuser->setAdresse('123 Rue de l\'Admin');
            $superuser->setRole('ROLE_SUPER_ADMIN');

            // Sauvegarder l'utilisateur
            $entityManager->persist($superuser);
            $entityManager->flush();

            $this->addFlash('success', 'Superuser créé avec succès !');
            return new RedirectResponse($this->generateUrl('app_login'));

        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de la création du superuser : ' . $e->getMessage());
            return new RedirectResponse($this->generateUrl('app_login'));
        }
    }
}
