<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Notification;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

#[AsEventListener(event: KernelEvents::RESPONSE, priority: -10)]
final class NotificationSubscriber
{
    public function __construct(
        private readonly NotificationManager $manager,
    ) {
    }

    public function __invoke(ResponseEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        if (!$request->hasSession()) {
            return;
        }

        $session = $request->getSession();
        if (!$session instanceof Session) {
            return;
        }

        $bag = $session->getFlashBag();

        foreach ($bag->all() as $type => $messages) {
            $notificationType = NotificationType::tryFrom($type);
            if (null === $notificationType) {
                continue;
            }

            foreach ($messages as $message) {
                if ($message instanceof Notification) {
                    continue;
                }

                $notification = match ($notificationType) {
                    NotificationType::Success => Notification::success((string) $message),
                    NotificationType::Error => Notification::error((string) $message),
                    NotificationType::Warning => Notification::warning((string) $message),
                    NotificationType::Info => Notification::info((string) $message),
                };

                $this->manager->add($notification);
            }
        }
    }
}
