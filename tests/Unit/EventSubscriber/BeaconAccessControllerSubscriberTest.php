<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\EventSubscriber;

use Devgeek\BeaconAdmin\EventSubscriber\BeaconAccessControllerSubscriber;
use Devgeek\BeaconAdmin\Security\BeaconAccess;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

final class BeaconAccessControllerSubscriberTest extends TestCase
{
    private function createSubscriber(
        AuthorizationCheckerInterface $authorizationChecker,
    ): BeaconAccessControllerSubscriber {
        return new BeaconAccessControllerSubscriber($authorizationChecker);
    }

    private function createControllerEvent(
        mixed $controller,
        int $requestType = HttpKernelInterface::MAIN_REQUEST,
    ): ControllerEvent {
        return new ControllerEvent(
            $this->createMock(HttpKernelInterface::class),
            $controller,
            Request::create('/admin'),
            $requestType,
        );
    }

    #[Test]
    public function itSkipsNonMainRequests(): void
    {
        $authorizationChecker = $this->createMock(AuthorizationCheckerInterface::class);
        $authorizationChecker->expects($this->never())->method('isGranted');

        $subscriber = $this->createSubscriber($authorizationChecker);
        $controller = new #[BeaconAccess(role: 'ROLE_ADMIN')] class {
            public function __invoke(): void {}
        };

        $event = $this->createControllerEvent($controller, HttpKernelInterface::SUB_REQUEST);

        // Should not throw — sub-requests are skipped
        $subscriber($event);
        $this->addToAssertionCount(1);
    }

    #[Test]
    public function itHandlesClosureControllers(): void
    {
        $authorizationChecker = $this->createMock(AuthorizationCheckerInterface::class);
        $authorizationChecker->expects($this->never())->method('isGranted');

        $subscriber = $this->createSubscriber($authorizationChecker);
        // A Closure is an invokable object — the subscriber resolves it
        // but finds no BeaconAccess attributes, so it passes through.
        $event = $this->createControllerEvent(fn () => null);

        $subscriber($event);
        $this->addToAssertionCount(1);
    }

    #[Test]
    public function itAllowsAccessWhenIsGrantedReturnsTrue(): void
    {
        $authorizationChecker = $this->createMock(AuthorizationCheckerInterface::class);
        $authorizationChecker->method('isGranted')->willReturn(true);

        $subscriber = $this->createSubscriber($authorizationChecker);
        $controller = new #[BeaconAccess(role: 'ROLE_ADMIN')] class {
            public function __invoke(): void {}
        };

        $event = $this->createControllerEvent($controller);

        $subscriber($event);
        $this->addToAssertionCount(1);
    }

    #[Test]
    public function itDeniesAccessWhenIsGrantedReturnsFalse(): void
    {
        $authorizationChecker = $this->createMock(AuthorizationCheckerInterface::class);
        $authorizationChecker->method('isGranted')->willReturn(false);

        $subscriber = $this->createSubscriber($authorizationChecker);
        $controller = new #[BeaconAccess(role: 'ROLE_ADMIN')] class {
            public function __invoke(): void {}
        };

        $event = $this->createControllerEvent($controller);

        $this->expectException(AccessDeniedException::class);
        $this->expectExceptionMessage('Access denied: requires role "ROLE_ADMIN".');

        $subscriber($event);
    }

    #[Test]
    public function itDeniesAccessWhenAuthorizationCheckerThrows(): void
    {
        $authorizationChecker = $this->createMock(AuthorizationCheckerInterface::class);
        $authorizationChecker->method('isGranted')
            ->willThrowException(new \RuntimeException('No authentication context'));

        $subscriber = $this->createSubscriber($authorizationChecker);
        $controller = new #[BeaconAccess(role: 'ROLE_ADMIN')] class {
            public function __invoke(): void {}
        };

        $event = $this->createControllerEvent($controller);

        $this->expectException(AccessDeniedException::class);

        $subscriber($event);
    }

    #[Test]
    public function itPassesThroughControllersWithoutBeaconAccessAttribute(): void
    {
        $authorizationChecker = $this->createMock(AuthorizationCheckerInterface::class);
        $authorizationChecker->expects($this->never())->method('isGranted');

        $subscriber = $this->createSubscriber($authorizationChecker);
        $controller = new class {
            public function __invoke(): void {}
        };

        $event = $this->createControllerEvent($controller);

        $subscriber($event);
        $this->addToAssertionCount(1);
    }

    #[Test]
    public function itChecksMethodLevelBeaconAccessAttribute(): void
    {
        $authorizationChecker = $this->createMock(AuthorizationCheckerInterface::class);
        $authorizationChecker->method('isGranted')->willReturn(false);

        $subscriber = $this->createSubscriber($authorizationChecker);

        $controller = new class {
            #[BeaconAccess(role: 'ROLE_SUPER_ADMIN')]
            public function edit(): void {}
        };

        $event = $this->createControllerEvent([$controller, 'edit']);

        $this->expectException(AccessDeniedException::class);
        $this->expectExceptionMessage('Access denied: requires role "ROLE_SUPER_ADMIN".');

        $subscriber($event);
    }

    #[Test]
    public function itChecksBothClassAndMethodLevelAttributes(): void
    {
        $authorizationChecker = $this->createMock(AuthorizationCheckerInterface::class);
        // First call for class-level attribute — granted
        // Second call for method-level attribute — denied
        $authorizationChecker->expects($this->exactly(2))
            ->method('isGranted')
            ->willReturnOnConsecutiveCalls(true, false);

        $subscriber = $this->createSubscriber($authorizationChecker);

        $controller = new #[BeaconAccess(role: 'ROLE_ADMIN')] class {
            #[BeaconAccess(role: 'ROLE_SUPER_ADMIN')]
            public function edit(): void {}
        };

        $event = $this->createControllerEvent([$controller, 'edit']);

        $this->expectException(AccessDeniedException::class);
        $this->expectExceptionMessage('Access denied: requires role "ROLE_SUPER_ADMIN".');

        $subscriber($event);
    }

    #[Test]
    public function itDeniesAccessWhenRoleIsNull(): void
    {
        $authorizationChecker = $this->createMock(AuthorizationCheckerInterface::class);
        $authorizationChecker->method('isGranted')->willReturn(false);

        $subscriber = $this->createSubscriber($authorizationChecker);
        $controller = new #[BeaconAccess] class {
            public function __invoke(): void {}
        };

        $event = $this->createControllerEvent($controller);

        $this->expectException(AccessDeniedException::class);
        $this->expectExceptionMessage('Access denied.');

        $subscriber($event);
    }

    #[Test]
    public function itChecksArrayStyleController(): void
    {
        $authorizationChecker = $this->createMock(AuthorizationCheckerInterface::class);
        $authorizationChecker->method('isGranted')->willReturn(false);

        $subscriber = $this->createSubscriber($authorizationChecker);

        $controller = new #[BeaconAccess(role: 'ROLE_ADMIN')] class {
            public function customMethod(): void {}
        };

        $event = $this->createControllerEvent([$controller, 'customMethod']);

        $this->expectException(AccessDeniedException::class);

        $subscriber($event);
    }
}
