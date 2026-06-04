<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Notification;

use Devgeek\BeaconAdmin\Notification\Notification;
use Devgeek\BeaconAdmin\Notification\NotificationManager;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

final class NotificationManagerTest extends TestCase
{
    private function createManager(): NotificationManager
    {
        $session = new Session(new MockArraySessionStorage());
        $session->start();

        $request = new Request();
        $request->setSession($session);

        $stack = new RequestStack();
        $stack->push($request);

        return new NotificationManager($stack);
    }

    #[Test]
    public function peekReturnsNotificationsWithoutRemoving(): void
    {
        $manager = $this->createManager();
        $manager->add(Notification::success('First'));
        $manager->add(Notification::error('Second'));

        $first = $manager->peek();
        $second = $manager->peek();

        $this->assertCount(2, $first);
        $this->assertCount(2, $second, 'peek should not consume notifications');
    }

    #[Test]
    public function consumeRemovesNotifications(): void
    {
        $manager = $this->createManager();
        $manager->add(Notification::success('One'));
        $manager->add(Notification::warning('Two'));

        $consumed = $manager->consume();
        $this->assertCount(2, $consumed);

        $this->assertSame([], $manager->peek());
        $this->assertSame([], $manager->consume());
    }

    #[Test]
    public function peekSkipsNonNotificationItems(): void
    {
        $manager = $this->createManager();

        $session = (new \ReflectionMethod($manager, 'getSession'))->invoke($manager);

        $session->set(NotificationManager::SESSION_KEY, [
            Notification::success('Valid'),
            'string-not-a-notification',
            new \stdClass(),
        ]);

        $result = $manager->peek();

        $this->assertCount(1, $result);
        $this->assertSame('Valid', $result[0]->message);
    }

    #[Test]
    public function peekReturnsEmptyWhenSessionMissing(): void
    {
        $stack = new RequestStack();
        $manager = new NotificationManager($stack);

        $this->assertSame([], $manager->peek());
    }
}
