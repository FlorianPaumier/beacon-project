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
    private AuthorizationCheckerInterface $authorizationChecker;
    private HttpKernelInterface $kernel;

    protected function setUp(): void
    {
        $this->authorizationChecker = $this->createMock(AuthorizationCheckerInterface::class);
        $this->kernel = $this->createMock(HttpKernelInterface::class);
    }

    private function createSubscriber(): BeaconAccessControllerSubscriber
    {
        return new BeaconAccessControllerSubscriber($this->authorizationChecker);
    }

    private function createControllerEvent(
        mixed $controller,
        int $requestType = HttpKernelInterface::MAIN_REQUEST,
    ): ControllerEvent {
        return new ControllerEvent(
            $this->kernel,
            $controller,
            Request::create('/admin'),
            $requestType,
        );
    }

    #[Test]
    public function itSkipsNonMainRequests(): void
    {
        $this->authorizationChecker->expects($this->never())->method('isGranted');

        $subscriber = $this->createSubscriber();
        $controller = new #[BeaconAccess(role: 'ROLE_ADMIN')] class {
            public function __invoke(): void {}
        };

        $event = $this->createControllerEvent($controller, HttpKernelInterface::SUB_REQUEST);

        // Should not throw
        $subscriber($event);

        $this->assertTrue(true); // If we reach here, no exception was thrown
    }

    #[Test]
    public function itHandlesClosureControllers(): void
    {
        $this->authorizationChecker->expects($this->never())->method('isGranted');

        $subscriber = $this->createSubscriber();
        // A Closure is an invokable object — the subscriber resolves it
        // but finds no BeaconAccess attributes, so it passes through.
        $event = $this->createControllerEvent(fn () => null);

        // Should not throw
        $subscriber($event);

        $this->assertTrue(true);
    }

    #[Test]
    public function itAllowsAccessWhenIsGrantedReturnsTrue(): void
    {
        $this->authorizationChecker
            ->method('isGranted')
            ->willReturn(true);

        $subscriber = $this->createSubscriber();
        $controller = new #[BeaconAccess(role: 'ROLE_ADMIN')] class {
            public function __invoke(): void {}
        };

        $event = $this->createControllerEvent($controller);

        // Should not throw
        $subscriber($event);

        $this->assertTrue(true);
    }

    #[Test]
    public function itDeniesAccessWhenIsGrantedReturnsFalse(): void
    {
        $this->authorizationChecker
            ->method('isGranted')
            ->willReturn(false);

        $subscriber = $this->createSubscriber();
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
        $this->authorizationChecker
            ->method('isGranted')
            ->willThrowException(new \RuntimeException('No authentication context'));

        $subscriber = $this->createSubscriber();
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
        $this->authorizationChecker->expects($this->never())->method('isGranted');

        $subscriber = $this->createSubscriber();
        $controller = new class {
            public function __invoke(): void {}
        };

        $event = $this->createControllerEvent($controller);

        // Should not throw
        $subscriber($event);

        $this->assertTrue(true);
    }

    #[Test]
    public function itChecksMethodLevelBeaconAccessAttribute(): void
    {
        $this->authorizationChecker
            ->method('isGranted')
            ->willReturn(false);

        $subscriber = $this->createSubscriber();

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
        // First call for class-level attribute — granted
        // Second call for method-level attribute — denied
        $this->authorizationChecker
            ->expects($this->exactly(2))
            ->method('isGranted')
            ->willReturnOnConsecutiveCalls(true, false);

        $subscriber = $this->createSubscriber();

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
        $this->authorizationChecker
            ->method('isGranted')
            ->willReturn(false);

        $subscriber = $this->createSubscriber();
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
        $this->authorizationChecker
            ->method('isGranted')
            ->willReturn(false);

        $subscriber = $this->createSubscriber();

        $controller = new #[BeaconAccess(role: 'ROLE_ADMIN')] class {
            public function customMethod(): void {}
        };

        $event = $this->createControllerEvent([$controller, 'customMethod']);

        $this->expectException(AccessDeniedException::class);

        $subscriber($event);
    }
}
