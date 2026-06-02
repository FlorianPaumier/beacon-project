<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Security;

use Devgeek\BeaconAdmin\Security\LoginFormAuthenticator;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;

final class LoginFormAuthenticatorTest extends TestCase
{
    #[Test]
    public function itSupportsPostRequestToLoginRoute(): void
    {
        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $authenticator = LoginFormAuthenticator::make($urlGenerator);

        $request = new Request();
        $request->setMethod('POST');
        $request->attributes->set('_route', 'beacon_admin_login');

        $this->assertTrue($authenticator->supports($request));
    }

    #[Test]
    public function itDoesNotSupportGetRequest(): void
    {
        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $authenticator = LoginFormAuthenticator::make($urlGenerator);

        $request = new Request();
        $request->setMethod('GET');
        $request->attributes->set('_route', 'beacon_admin_login');

        $this->assertFalse($authenticator->supports($request));
    }

    #[Test]
    public function itDoesNotSupportWrongRoute(): void
    {
        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $authenticator = LoginFormAuthenticator::make($urlGenerator);

        $request = new Request();
        $request->setMethod('POST');
        $request->attributes->set('_route', 'some_other_route');

        $this->assertFalse($authenticator->supports($request));
    }

    #[Test]
    public function itGeneratesLoginUrl(): void
    {
        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $urlGenerator->expects($this->once())
            ->method('generate')
            ->with('beacon_admin_login')
            ->willReturn('/admin/login');

        $authenticator = LoginFormAuthenticator::make($urlGenerator);

        $reflection = new \ReflectionMethod($authenticator, 'getLoginUrl');

        $this->assertSame('/admin/login', $reflection->invoke($authenticator, new Request()));
    }

    #[Test]
    public function itCreatesPassportOnAuthenticate(): void
    {
        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $authenticator = LoginFormAuthenticator::make($urlGenerator);

        $request = new Request();
        $request->request->set('_username', 'admin');
        $request->request->set('_password', 'secret');
        $request->request->set('_csrf_token', 'token123');

        $passport = $authenticator->authenticate($request);

        $this->assertTrue($passport->hasBadge(UserBadge::class));
        $this->assertTrue($passport->hasBadge(PasswordCredentials::class));
        $this->assertTrue($passport->hasBadge(CsrfTokenBadge::class));
    }

    #[Test]
    public function itRedirectsToDashboardOnSuccess(): void
    {
        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $urlGenerator->expects($this->once())
            ->method('generate')
            ->with('beacon_admin_dashboard')
            ->willReturn('/admin/dashboard');

        $authenticator = LoginFormAuthenticator::make($urlGenerator);

        $request = new Request();
        $request->setSession($this->createMock(SessionInterface::class));
        $token = $this->createMock(TokenInterface::class);

        $response = $authenticator->onAuthenticationSuccess($request, $token, 'admin');

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame('/admin/dashboard', $response->getTargetUrl());
    }

    #[Test]
    public function itRedirectsToLoginOnFailure(): void
    {
        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $urlGenerator->expects($this->once())
            ->method('generate')
            ->with('beacon_admin_login')
            ->willReturn('/admin/login');

        $authenticator = LoginFormAuthenticator::make($urlGenerator);

        $request = new Request();
        $session = $this->createMock(SessionInterface::class);
        $request->setSession($session);
        $exception = $this->createMock(AuthenticationException::class);
        $exception->method('getMessageKey')->willReturn('Invalid credentials.');

        $response = $authenticator->onAuthenticationFailure($request, $exception);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame('/admin/login', $response->getTargetUrl());
    }
}
