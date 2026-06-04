<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Notification;

use Symfony\Component\HttpFoundation\RequestStack;

final class NotificationManager
{
    public const SESSION_KEY = 'beacon_notifications';

    public function __construct(
        private readonly RequestStack $requestStack,
    ) {
    }

    public function add(Notification $notification): void
    {
        $session = $this->getSession();
        if (null === $session) {
            return;
        }

        $items = $session->get(self::SESSION_KEY, []);
        $items[] = $notification;
        $session->set(self::SESSION_KEY, $items);
    }

    /** @return list<Notification> */
    public function consume(): array
    {
        $session = $this->getSession();
        if (null === $session) {
            return [];
        }

        $items = $session->get(self::SESSION_KEY, []);
        if (!is_array($items)) {
            return [];
        }

        $session->remove(self::SESSION_KEY);

        $notifications = [];
        foreach ($items as $item) {
            if ($item instanceof Notification) {
                $notifications[] = $item;
            }
        }

        return $notifications;
    }

    /** @return list<Notification> */
    public function peek(): array
    {
        $session = $this->getSession();
        if (null === $session) {
            return [];
        }

        $items = $session->get(self::SESSION_KEY, []);
        if (!is_array($items)) {
            return [];
        }

        $notifications = [];
        foreach ($items as $item) {
            if ($item instanceof Notification) {
                $notifications[] = $item;
            }
        }

        return $notifications;
    }

    public function count(): int
    {
        return count($this->peek());
    }

    public function clear(): void
    {
        $session = $this->getSession();
        if (null === $session) {
            return;
        }

        $session->remove(self::SESSION_KEY);
    }

    /** @return list<Notification> */
    public function all(): array
    {
        return $this->consume();
    }

    public function markAsRead(int $index): void
    {
        $session = $this->getSession();
        if (null === $session) {
            return;
        }

        $items = $session->get(self::SESSION_KEY, []);
        unset($items[$index]);
        $session->set(self::SESSION_KEY, array_values($items));
    }

    private function getSession(): ?\Symfony\Component\HttpFoundation\Session\SessionInterface
    {
        $request = $this->requestStack->getMainRequest();
        if (null === $request || !$request->hasSession()) {
            return null;
        }

        return $request->getSession();
    }
}
