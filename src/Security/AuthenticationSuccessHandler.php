<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationSuccessHandler;
use Symfony\Component\Security\Http\HttpUtils;
use Psr\Log\LoggerInterface;

class AuthenticationSuccessHandler extends DefaultAuthenticationSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    public function __construct(
        protected HttpUtils $httpUtils,
        array $options = [],
        protected ?LoggerInterface $logger = null,
    ) {
        parent::__construct($httpUtils, $options, $logger);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): ?RedirectResponse
    {
        $user = $token->getUser();
        
        if ($user instanceof User) {
            if ($user->isAdmin()) {
                return new RedirectResponse($request->getSchemeAndHttpHost() . '/admin/dashboard');
            } elseif ($user->isAgent()) {
                return new RedirectResponse($request->getSchemeAndHttpHost() . '/agent/dashboard');
            } else {
                return new RedirectResponse($request->getSchemeAndHttpHost() . '/citoyen/dashboard');
            }
        }
        
        // Si l'utilisateur n'est pas une instance de User, rediriger vers le tableau de bord par défaut
        return new RedirectResponse($request->getSchemeAndHttpHost() . '/dashboard');
    }
}
