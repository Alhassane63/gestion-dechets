<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class RedirectController extends AbstractController
{
    #[Route('/register/role', name: 'app_register_role_redirect')]
    public function redirectToRegisterForm(): RedirectResponse
    {
        // Redirection vers le formulaire d'inscription avec le rôle approprié
        return $this->redirectToRoute('app_register_form', ['role' => 'ROLE_CITOYEN']);
    }
}
