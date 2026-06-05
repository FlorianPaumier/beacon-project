<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Functional\Notification;

use Devgeek\BeaconAdmin\Notification\Notification;
use Devgeek\BeaconAdmin\Notification\NotificationManager;
use Devgeek\BeaconAdmin\Tests\Fixtures\TestApp\TestKernel;
use Devgeek\BeaconAdmin\Tests\Functional\BeaconWebTestCase;
use Symfony\Component\Security\Core\User\InMemoryUser;

final class NotificationControllerTest extends BeaconWebTestCase
{
    protected static function getKernelClass(): string
    {
        return TestKernel::class;
    }

    public function testUnreadCountIsAccessibleToAdmin(): void
    {
        $client = static::createClient();
        $client->loginUser(new InMemoryUser('admin_user', 'admin_pass', ['ROLE_ADMIN']));
        $client->request('GET', '/admin/notifications/unread-count');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        $payload = json_decode((string) $client->getResponse()->getContent(), true);
        $this->assertIsArray($payload);
        $this->assertArrayHasKey('count', $payload);
        $this->assertArrayHasKey('items', $payload);
        $this->assertSame(0, $payload['count']);
        $this->assertSame([], $payload['items']);
    }

    public function testUnreadCountReturnsPendingNotificationsAsItems(): void
    {
        $client = static::createClient();
        $client->loginUser(new InMemoryUser('admin_user', 'admin_pass', ['ROLE_ADMIN']));

        $client->request('GET', '/admin');

        $session = $client->getSession();
        $session->set(NotificationManager::SESSION_KEY, [
            Notification::success('Item saved'),
            Notification::error('Validation failed'),
            Notification::warning('Quota low'),
            Notification::info('New version available'),
        ]);
        $session->save();

        $client->request('GET', '/admin/notifications/unread-count');

        $this->assertResponseIsSuccessful();

        $payload = json_decode((string) $client->getResponse()->getContent(), true);
        $this->assertIsArray($payload);
        $this->assertSame(4, $payload['count']);
        $this->assertCount(4, $payload['items']);

        $types = array_map(static fn (array $i) => $i['type'], $payload['items']);
        $this->assertContains('success', $types);
        $this->assertContains('error', $types);
        $this->assertContains('warning', $types);
        $this->assertContains('info', $types);
    }

    public function testUnreadCountRedirectsUnauthenticatedUserToLogin(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin/notifications/unread-count');

        $this->assertResponseRedirects('/en/admin/login');
    }

    public function testUnreadCountIsForbiddenForNonAdminUser(): void
    {
        $client = static::createClient();
        $client->loginUser(new InMemoryUser('regular_user', 'user_pass', ['ROLE_USER']));
        $client->request('GET', '/admin/notifications/unread-count');

        $this->assertResponseStatusCodeSame(403);
    }

    public function testRouteNameIsBeaconsNotificationsUnreadCount(): void
    {
        $client = static::createClient();
        $router = $client->getContainer()->get('router');
        $route = $router->getRouteCollection()->get('beacon_admin_notifications_unread_count');

        $this->assertNotNull($route);
        $this->assertSame('/admin/notifications/unread-count', $route->getPath());
        $this->assertSame(['GET'], $route->getMethods());
    }

    public function testUnreadCountDoesNotConsumeNotifications(): void
    {
        $client = static::createClient();
        $client->loginUser(new InMemoryUser('admin_user', 'admin_pass', ['ROLE_ADMIN']));

        $client->request('GET', '/admin');
        $session = $client->getSession();
        $session->set(NotificationManager::SESSION_KEY, [
            Notification::success('Persistent message'),
        ]);
        $session->save();

        $client->request('GET', '/admin/notifications/unread-count');
        $this->assertResponseIsSuccessful();
        $payload = json_decode((string) $client->getResponse()->getContent(), true);
        $this->assertSame(1, $payload['count']);

        $client->request('GET', '/admin/notifications/unread-count');
        $this->assertResponseIsSuccessful();
        $payload = json_decode((string) $client->getResponse()->getContent(), true);
        $this->assertSame(1, $payload['count'], 'Notifications should persist across polls');
    }
}
