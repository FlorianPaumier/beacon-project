<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\EventSubscriber;

use Devgeek\BeaconAdmin\Security\BeaconAccess;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[AsEventListener(event: KernelEvents::CONTROLLER, priority: 25)]
final class BeaconAccessControllerSubscriber
{
    public function __construct(
        private readonly AuthorizationCheckerInterface $authorizationChecker,
    ) {
    }

    public function __invoke(ControllerEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $controller = $event->getController();

        // Symfony returns [object, method] for class-based controllers,
        // or an invokable object directly for controllers registered as
        // services (e.g., with #[AsController]).
        if (\is_array($controller)) {
            $controllerObject = $controller[0];
            $methodName = $controller[1];
        } elseif (\is_object($controller)) {
            $controllerObject = $controller;
            $methodName = '__invoke';
        } else {
            return;
        }

        $class = new \ReflectionClass($controllerObject);
        $method = $class->getMethod($methodName);

        // Collect BeaconAccess attributes from class and method
        $reflectionAttributes = [
            ...$class->getAttributes(BeaconAccess::class),
            ...$method->getAttributes(BeaconAccess::class),
        ];

        foreach ($reflectionAttributes as $reflectionAttribute) {
            /** @var BeaconAccess $instance */
            $instance = $reflectionAttribute->newInstance();

            try {
                $isGranted = $this->authorizationChecker->isGranted($instance);
            } catch (\Exception) {
                // No authentication context or voter error — treat as access denied
                $isGranted = false;
            }

            if (true !== $isGranted) {
                $role = $instance->getRole();
                $message = null !== $role
                    ? \sprintf('Access denied: requires role "%s".', $role)
                    : 'Access denied.';

                throw new AccessDeniedException($message);
            }
        }
    }
}
