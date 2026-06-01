<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Functional;

use Devgeek\BeaconAdmin\Tests\Fixtures\TestApp\TestKernel;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class BundleBootTest extends WebTestCase
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
        $this->assertTrue($container->getParameter('beacon_admin.theme.dark_mode'));
    }

    public function testWidgetRegistryIsAvailable(): void
    {
        $client = static::createClient();
        $container = $client->getContainer();

        $registry = $container->get('Devgeek\BeaconAdmin\Widget\WidgetRegistry');
        $this->assertSame([], $registry->all(), 'Widget registry should start empty in a fresh boot.');
    }

    public function testMenuBuilderIsAvailable(): void
    {
        $client = static::createClient();
        $container = $client->getContainer();

        $menuBuilder = $container->get('Devgeek\BeaconAdmin\Menu\MenuBuilder');
        $this->assertSame([], $menuBuilder->build(), 'Menu should be empty with no items configured.');
    }

    public function testDashboardRouteIsRegistered(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin');

        // Without any widgets, the response will be 200 but may show an empty state.
        // If no route is registered, we get a 404 instead.
        $this->assertResponseIsSuccessful();
    }
}
