<?php

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class LoginFormAuthenticator extends AbstractLoginFormAuthenticator implements AuthenticationEntryPointInterface
{
    public const LOGIN_ROUTE = 'app_login';

    private EntityManagerInterface $entityManager;
    private UrlGeneratorInterface $urlGenerator;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator, UserPasswordHasherInterface $passwordHasher)
    {
        $this->entityManager = $entityManager;
        $this->urlGenerator = $urlGenerator;
        $this->passwordHasher = $passwordHasher;
    }

    public function authenticate(Request $request): Passport
    {
        $email = $request->request->get('_username', '');
        $request->getSession()->set('security.last_username', $email);
        $password = $request->request->get('_password', '');

        return new Passport(
            new UserBadge($email, function ($userIdentifier) use ($password) {
                $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $userIdentifier]);
                
                if (!$user) {
                    throw new CustomUserMessageAuthenticationException('Email non trouvé');
                }

                if (!$this->passwordHasher->isPasswordValid($user, $password)) {
                    throw new CustomUserMessageAuthenticationException('Mot de passe incorrect');
                }

                return $user;
            }),
            new PasswordCredentials($password),
            [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $user = $token->getUser();
        
        if ($user instanceof User) {
            $roles = $user->getRoles();
            
            // Redirection basée sur le rôle
            if (in_array('ROLE_ADMIN', $roles)) {
                return new RedirectResponse($this->urlGenerator->generate('app_dashboard'));
            } elseif (in_array('ROLE_AGENT', $roles)) {
                return new RedirectResponse($this->urlGenerator->generate('app_dashboard_agent'));
            } elseif (in_array('ROLE_CITOYEN', $roles)) {
                return new RedirectResponse($this->urlGenerator->generate('app_dashboard_citoyen'));
            }
        }
        
        // Si on n'a pas pu déterminer le rôle, retour à la page de connexion
        return new RedirectResponse($this->urlGenerator->generate('app_login'));
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }

    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        return new RedirectResponse($this->getLoginUrl($request));
    }

    public function supports(Request $request): bool
    {
        return self::LOGIN_ROUTE === $request->attributes->get('_route')
            && $request->isMethod('POST');
    }
}
