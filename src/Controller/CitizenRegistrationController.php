<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class CitizenRegistrationController extends AbstractController
{
    #[Route('/citizen/register', name: 'citizen_register', methods: ['GET', 'POST'])]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager
    ): Response {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user, [
            'role' => 'ROLE_CITOYEN',
            'data_class' => User::class
        ]);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                // Hasher le mot de passe
                $plainPassword = $form->get('plainPassword')->getData();
                $hashedPassword = $userPasswordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);
                
                // Définir le rôle
                $user->setRoles(['ROLE_CITOYEN']);
                
                // Persister l'utilisateur
                $entityManager->persist($user);
                $entityManager->flush();

                // Ajouter un message de succès
                $this->addFlash('success', 'Votre compte a été créé avec succès ! Vous pouvez maintenant vous connecter.');

                // Redirection vers la page de connexion
                return $this->redirectToRoute('app_login');

            } catch (\Exception $e) {
                // Ajouter un message d'erreur
                $this->addFlash('error', 'Une erreur est survenue lors de la création du compte. Veuillez réessayer.');
                
                // Redirection vers le formulaire d'inscription
                return $this->redirectToRoute('citizen_register');
            }
        }

        return $this->render('registration/citizen_register.html.twig', [
            'registrationForm' => $form->createView()
        ]);
    }
}
