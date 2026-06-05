<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\EventSubscriber;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

#[AsEventListener(event: KernelEvents::EXCEPTION, priority: 2)]
final class LoginRedirectSubscriber
{
    use TargetPathTrait;

    /**
     * @param string[] $locales
     */
    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly TokenStorageInterface $tokenStorage,
        private readonly bool $enabled = true,
        private readonly string $loginRoute = 'beacon_admin_login',
        private readonly string $routePrefix = '/admin',
        private readonly string $firewallName = 'admin',
        private readonly array $locales = [],
    ) {
    }

    public function __invoke(ExceptionEvent $event): void
    {
        if (!$this->enabled) {
            return;
        }

        $exception = $event->getThrowable();

        if (!$exception instanceof AccessDeniedException) {
            return;
        }

        $request = $event->getRequest();

        // Only intercept routes under the admin prefix (e.g. /admin or /en/admin)
        $path = $request->getPathInfo();
        if (!str_starts_with($path, $this->routePrefix)
            && !preg_match('#^/[a-z]{2}(_[A-Z]{2})?'.preg_quote($this->routePrefix, '#').'#', $path)) {
            return;
        }

        // Skip the login route itself to prevent redirect loops
        if ($request->attributes->get('_route') === $this->loginRoute) {
            return;
        }

        // Let authenticated users (just missing role) get a 403;
        // token is null only for truly unauthenticated requests
        if (null !== $this->tokenStorage->getToken()) {
            return;
        }

        // Preserve the originally requested URL so LoginFormAuthenticator redirects back after login
        $this->saveTargetPath($request->getSession(), $this->firewallName, $request->getUri());

        // Use the browser's preferred language, falling back to the request locale
        if ([] !== $this->locales) {
            $locale = $request->getPreferredLanguage($this->locales);
        } else {
            $preferred = $request->getPreferredLanguage();
            $locale = '' !== $preferred ? $preferred : $request->getLocale();
            // Strip region to get the language code (e.g. "fr" from "fr_FR")
            $locale = strtok($locale, '_');
        }

        $event->setResponse(new RedirectResponse(
            $this->urlGenerator->generate($this->loginRoute, [
                '_locale' => $locale,
            ]),
        ));
    }
}
