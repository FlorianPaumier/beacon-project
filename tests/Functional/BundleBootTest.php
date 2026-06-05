<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Functional;

use Devgeek\BeaconAdmin\Tests\Fixtures\TestApp\TestKernel;
use Symfony\Component\Security\Core\User\InMemoryUser;

final class BundleBootTest extends BeaconWebTestCase
{
    protected static function getKernelClass(): string
    {
        return TestKernel::class;
    }

    public function testBundleBootsAndConfigIsLoaded(): void
    {
        $client = static::createClient();
        $container = $client->getContainer();

        $this->assertSame('/admin', $container->getParameter('beacon_admin.route_prefix'));
        $this->assertSame('Beacon Admin', $container->getParameter('beacon_admin.title'));
        $this->assertSame('modern', $container->getParameter('beacon_admin.default_theme'));
        $this->assertSame('ROLE_ADMIN', $container->getParameter('beacon_admin.security.role'));
    }

    public function testWidgetRegistryIsAvailable(): void
    {
        $client = static::createClient();
        $container = $client->getContainer();

        $registry = $container->get('Devgeek\BeaconAdmin\Widget\WidgetRegistry');
        $this->assertSame([], $registry->all());
    }

    public function testMenuBuilderIsAvailable(): void
    {
        $client = static::createClient();
        $container = $client->getContainer();

        $menuBuilder = $container->get('Devgeek\BeaconAdmin\Menu\MenuBuilder');
        $this->assertSame([], $menuBuilder->build());
    }

    public function testDashboardRouteIsRegistered(): void
    {
        $client = static::createClient();
        $client->loginUser(new InMemoryUser('admin_user', 'admin_pass', ['ROLE_ADMIN']));
        $client->request('GET', '/admin');

        $this->assertResponseIsSuccessful();
    }
}
