<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\EventSubscriber;

use Devgeek\BeaconAdmin\EventSubscriber\LoginRedirectSubscriber;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\User\InMemoryUser;

final class LoginRedirectSubscriberTest extends TestCase
{
    /**
     * @param array<string, mixed> $overrides
     */
    private function createSubscriber(
        UrlGeneratorInterface $urlGenerator,
        TokenStorageInterface $tokenStorage,
        array $overrides = [],
    ): LoginRedirectSubscriber {
        return new LoginRedirectSubscriber(
            urlGenerator: $urlGenerator,
            tokenStorage: $tokenStorage,
            enabled: $overrides['enabled'] ?? true,
            loginRoute: $overrides['loginRoute'] ?? 'beacon_admin_login',
            routePrefix: $overrides['routePrefix'] ?? '/admin',
            firewallName: $overrides['firewallName'] ?? 'admin',
            locales: $overrides['locales'] ?? [],
        );
    }

    private function createExceptionEvent(
        \Throwable $exception,
        Request $request,
        int $requestType = HttpKernelInterface::MAIN_REQUEST,
    ): ExceptionEvent {
        return new ExceptionEvent($this->createMock(HttpKernelInterface::class), $request, $requestType, $exception);
    }

    private function createRequest(string $path, string $route = 'some_admin_route'): Request
    {
        $request = Request::create($path);
        $request->setSession(new Session(new MockArraySessionStorage()));
        $request->attributes->set('_route', $route);

        return $request;
    }

    #[Test]
    public function itRedirectsUnauthenticatedUserToLoginForAdminPath(): void
    {
        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $tokenStorage->method('getToken')->willReturn(null);

        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $urlGenerator->method('generate')
            ->with('beacon_admin_login', ['_locale' => 'en'])
            ->willReturn('/en/admin/login');

        $subscriber = $this->createSubscriber($urlGenerator, $tokenStorage);
        $request = $this->createRequest('/admin');
        $event = $this->createExceptionEvent(new AccessDeniedException(), $request);

        $subscriber($event);

        $this->assertTrue($event->hasResponse());
        $this->assertTrue($event->getResponse()->isRedirect('/en/admin/login'));
    }

    #[Test]
    public function itDoesNotRedirectWhenSubscriberIsDisabled(): void
    {
        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $tokenStorage->expects($this->never())->method('getToken');

        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);

        $subscriber = $this->createSubscriber($urlGenerator, $tokenStorage, ['enabled' => false]);
        $request = $this->createRequest('/admin');
        $event = $this->createExceptionEvent(new AccessDeniedException(), $request);

        $subscriber($event);

        $this->assertFalse($event->hasResponse());
    }

    #[Test]
    public function itDoesNotRedirectForNonAccessDeniedException(): void
    {
        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $tokenStorage->expects($this->never())->method('getToken');

        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);

        $subscriber = $this->createSubscriber($urlGenerator, $tokenStorage);
        $request = $this->createRequest('/admin');
        $event = $this->createExceptionEvent(new \RuntimeException('Some other error'), $request);

        $subscriber($event);

        $this->assertFalse($event->hasResponse());
    }

    #[Test]
    public function itDoesNotRedirectForNonAdminPath(): void
    {
        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $tokenStorage->expects($this->never())->method('getToken');

        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);

        $subscriber = $this->createSubscriber($urlGenerator, $tokenStorage);
        $request = $this->createRequest('/some-other-page', 'app_home');
        $event = $this->createExceptionEvent(new AccessDeniedException(), $request);

        $subscriber($event);

        $this->assertFalse($event->hasResponse());
    }

    #[Test]
    public function itDoesNotRedirectForLoginRouteItself(): void
    {
        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $tokenStorage->expects($this->never())->method('getToken');

        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);

        $subscriber = $this->createSubscriber($urlGenerator, $tokenStorage);
        $request = $this->createRequest('/admin/login', 'beacon_admin_login');
        $event = $this->createExceptionEvent(new AccessDeniedException(), $request);

        $subscriber($event);

        $this->assertFalse($event->hasResponse());
    }

    #[Test]
    public function itDoesNotRedirectWhenUserIsAuthenticated(): void
    {
        $user = new InMemoryUser('admin_user', 'admin_pass', ['ROLE_USER']);
        $token = new UsernamePasswordToken($user, 'admin', ['ROLE_USER']);

        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $tokenStorage->method('getToken')->willReturn($token);

        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);

        $subscriber = $this->createSubscriber($urlGenerator, $tokenStorage);
        $request = $this->createRequest('/admin');
        $event = $this->createExceptionEvent(new AccessDeniedException(), $request);

        $subscriber($event);

        $this->assertFalse($event->hasResponse());
    }

    #[Test]
    public function itHandlesLocalePrefixedAdminPath(): void
    {
        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $tokenStorage->method('getToken')->willReturn(null);

        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $urlGenerator->method('generate')
            ->with('beacon_admin_login', ['_locale' => 'fr'])
            ->willReturn('/fr/admin/login');

        $subscriber = $this->createSubscriber($urlGenerator, $tokenStorage);
        $request = $this->createRequest('/fr/admin', 'beacon_admin.dashboard_locale');
        $request->headers->set('Accept-Language', 'fr-FR,fr;q=0.9');
        $event = $this->createExceptionEvent(new AccessDeniedException(), $request);

        $subscriber($event);

        $this->assertTrue($event->hasResponse());
        $this->assertTrue($event->getResponse()->isRedirect('/fr/admin/login'));
    }

    #[Test]
    public function itHandlesLocaleWithRegionInAdminPath(): void
    {
        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $tokenStorage->method('getToken')->willReturn(null);

        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $urlGenerator->method('generate')
            ->with('beacon_admin_login', ['_locale' => 'en'])
            ->willReturn('/en/admin/login');

        $subscriber = $this->createSubscriber($urlGenerator, $tokenStorage);
        $request = $this->createRequest('/en_US/admin', 'beacon_admin.dashboard_locale');
        $request->headers->set('Accept-Language', 'en-US,en;q=0.9');
        $event = $this->createExceptionEvent(new AccessDeniedException(), $request);

        $subscriber($event);

        $this->assertTrue($event->hasResponse());
        $this->assertTrue($event->getResponse()->isRedirect('/en/admin/login'));
    }

    #[Test]
    public function itSavesTargetPathForPostLoginRedirect(): void
    {
        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $tokenStorage->method('getToken')->willReturn(null);

        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $urlGenerator->method('generate')->willReturn('/en/admin/login');

        $subscriber = $this->createSubscriber($urlGenerator, $tokenStorage, ['firewallName' => 'admin']);
        $request = $this->createRequest('/admin/some-page');
        $event = $this->createExceptionEvent(new AccessDeniedException(), $request);

        $subscriber($event);

        $session = $request->getSession();
        $this->assertSame(
            'http://localhost/admin/some-page',
            $session->get('_security.admin.target_path'),
        );
    }

    #[Test]
    public function itUsesConfiguredLocalesWhenProvided(): void
    {
        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $tokenStorage->method('getToken')->willReturn(null);

        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $urlGenerator->method('generate')
            ->with('beacon_admin_login', ['_locale' => 'de'])
            ->willReturn('/de/admin/login');

        $subscriber = $this->createSubscriber($urlGenerator, $tokenStorage, ['locales' => ['en', 'fr', 'de']]);
        $request = $this->createRequest('/admin');
        // de-DE has highest priority (no explicit q, defaults to 1.0)
        $request->headers->set('Accept-Language', 'de-DE,fr;q=0.8,en;q=0.5');
        $event = $this->createExceptionEvent(new AccessDeniedException(), $request);

        $subscriber($event);

        $this->assertTrue($event->hasResponse());
        $this->assertTrue($event->getResponse()->isRedirect('/de/admin/login'));
    }
}
