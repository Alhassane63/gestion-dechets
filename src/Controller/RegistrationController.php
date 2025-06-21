<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Form\AccountType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register/form/{role}', name: 'app_register_form', methods: ['GET', 'POST'])]
    public function registerForm(
        string $role,
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager
    ): Response {
        // Vérifier si le rôle est valide
        if (!in_array($role, ['ROLE_CITOYEN', 'ROLE_AGENT'])) {
            throw new \InvalidArgumentException('Type de compte invalide');
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user, [
            'role' => $role,
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
                $user->setRoles([$role]);
                
                // Persister l'utilisateur
                $entityManager->persist($user);
                $entityManager->flush();

                // Ajouter un message de succès
                $this->addFlash('success', 'Votre compte a été créé avec succès ! Vous pouvez maintenant vous connecter.');

                // Redirection vers la page de connexion
                return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);

            } catch (\Exception $e) {
                // Ajouter un message d'erreur
                $this->addFlash('error', 'Une erreur est survenue lors de la création du compte. Veuillez réessayer.');
                
                // Redirection vers la page de choix du type de compte
                return $this->redirectToRoute('app_register');
            }
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
            'role' => $role
        ]);
    }

    #[Route('/register', name: 'app_register', methods: ['GET', 'POST'])]
    public function register(Request $request): Response
    {
        $form = $this->createForm(AccountType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $accountType = $formData['accountType'];

            // Rediriger vers le formulaire d'inscription approprié
            return $this->redirectToRoute('app_register_form', [
                'role' => 'ROLE_' . strtoupper($accountType)
            ]);
        }

        return $this->render('registration/account_type.html.twig', [
            'accountTypeForm' => $form->createView()
        ]);
    }
}
