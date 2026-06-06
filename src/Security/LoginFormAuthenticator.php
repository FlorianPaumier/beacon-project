<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\FlashBagAwareSessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\ParameterBagUtils;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

/**
 * Built-in authenticator for the admin firewall.
 *
 * ## Enabling in an app
 *
 * 1. Enable in the bundle config:
 *    beacon_admin:
 *        security:
 *            use_builtin_authenticator: true
 *
 * 2. Reference in the app's security.yaml:
 *    firewalls:
 *        admin:
 *            custom_authenticator: Devgeek\BeaconAdmin\Security\LoginFormAuthenticator
 *            provider: app_user_provider
 *            logout: ...
 *
 * ## Overriding the login template
 *
 * Create templates/bundles/BeaconAdminBundle/security/login.html.twig
 * in your app — it automatically replaces the bundle's template.
 *
 * ## Replacing the authenticator entirely
 *
 * Keep use_builtin_authenticator: false (default), implement your own
 * authenticator, and reference it in security.yaml instead.
 */
class LoginFormAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    protected UrlGeneratorInterface $urlGenerator;
    protected string $loginRoute;
    protected string $afterLoginRedirect;
    protected string $firewallName;

    public function __construct(
        UrlGeneratorInterface $urlGenerator,
        string $loginRoute = 'beacon_admin_login',
        string $afterLoginRedirect = 'beacon_admin_dashboard',
        string $firewallName = 'admin',
    ) {
        $this->urlGenerator = $urlGenerator;
        $this->loginRoute = $loginRoute;
        $this->afterLoginRedirect = $afterLoginRedirect;
        $this->firewallName = $firewallName;
    }

    public static function make(
        UrlGeneratorInterface $urlGenerator,
        string $loginRoute = 'beacon_admin_login',
        string $afterLoginRedirect = 'beacon_admin_dashboard',
        string $firewallName = 'admin',
    ): self {
        return new self($urlGenerator, $loginRoute, $afterLoginRedirect, $firewallName);
    }

    public function supports(Request $request): bool
    {
        return $request->isMethod('POST')
            && $request->attributes->get('_route') === $this->loginRoute;
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate($this->loginRoute);
    }

    public function authenticate(Request $request): Passport
    {
        $username = ParameterBagUtils::getParameterBagValue($request->request, '_username');
        $password = ParameterBagUtils::getParameterBagValue($request->request, '_password');
        $csrfToken = ParameterBagUtils::getParameterBagValue($request->request, '_csrf_token');

        return new Passport(
            new UserBadge($username),
            new PasswordCredentials($password),
            [
                new CsrfTokenBadge('authenticate', $csrfToken),
                new RememberMeBadge(),
            ],
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $targetPath = $this->getTargetPath($request->getSession(), $firewallName);

        if (null !== $targetPath) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->urlGenerator->generate($this->afterLoginRedirect));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        $session = $request->getSession();

        if ($session instanceof FlashBagAwareSessionInterface) {
            $session->getFlashBag()->add('error', $exception->getMessageKey());
        }

        return new RedirectResponse($this->urlGenerator->generate($this->loginRoute));
    }
}
