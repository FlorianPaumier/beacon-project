<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Twig;

use Devgeek\BeaconAdmin\Tests\Fixtures\TestApp\TestKernel;
use Devgeek\BeaconAdmin\Twig\AdminRuntime;
use Devgeek\BeaconAdmin\Twig\BreadcrumbRenderer;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;

final class BreadcrumbRendererTest extends KernelTestCase
{
    protected function setUp(): void
    {
        $this->setInIsolation(true);

        parent::setUp();
    }

    protected static function getKernelClass(): string
    {
        return TestKernel::class;
    }

    public function testBuildReturnsEmptyArrayWhenNoRequest(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $requestStack = new RequestStack();
        $renderer = new BreadcrumbRenderer(
            $requestStack,
            $container->get(RouterInterface::class),
            new AdminRuntime([]),
        );

        $this->assertSame([], $renderer->build());
    }

    public function testBuildReturnsArrayForDashboardRoute(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $router = $container->get(RouterInterface::class);

        $request = Request::create('/admin');
        $request->attributes->set('_route', 'beacon_admin.dashboard');

        $requestStack = new RequestStack();
        $requestStack->push($request);

        $renderer = new BreadcrumbRenderer(
            $requestStack,
            $router,
            new AdminRuntime([]),
        );

        $trail = $renderer->build();

        $this->assertNotEmpty($trail);
        $this->assertNull($trail[array_key_last($trail)]['url']);
    }

    public function testLastItemAlwaysHasNullUrl(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $router = $container->get(RouterInterface::class);
        $requestStack = new RequestStack();

        $renderer = new BreadcrumbRenderer(
            $requestStack,
            $router,
            new AdminRuntime([]),
        );

        $request = Request::create('/admin/products/42/edit');
        $request->attributes->set('_route', 'beacon_admin_demo_product_edit');
        $requestStack->push($request);

        $trail = $renderer->build();

        $this->assertNotEmpty($trail);
        $this->assertNull(end($trail)['url']);
    }

    public function testFallsBackToHumanizedPathSegments(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $router = $container->get(RouterInterface::class);
        $requestStack = new RequestStack();

        $renderer = new BreadcrumbRenderer(
            $requestStack,
            $router,
            new AdminRuntime([]),
        );

        $request = Request::create('/admin/products/new');
        $requestStack->push($request);

        $trail = $renderer->build();

        $this->assertNotEmpty($trail);
        $labels = array_column($trail, 'label');
        $this->assertContains('Products', $labels);
        $this->assertContains('New', $labels);
        $this->assertSame('New', end($labels));
    }

    public function testMenuHierarchyIsUsedWhenRouteMatches(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $router = $container->get(RouterInterface::class);
        $requestStack = new RequestStack();

        $config = [
            'menu' => [
                [
                    'label' => 'Catalog',
                    'route' => null,
                    'children' => [
                        ['label' => 'Products', 'route' => 'beacon_admin_demo_product_list'],
                    ],
                ],
            ],
        ];

        $renderer = new BreadcrumbRenderer(
            $requestStack,
            $router,
            new AdminRuntime($config),
        );

        $request = Request::create('/admin/products');
        $request->attributes->set('_route', 'beacon_admin_demo_product_list');
        $requestStack->push($request);

        $trail = $renderer->build();

        $this->assertCount(2, $trail);
        $this->assertSame('Catalog', $trail[0]['label']);
        $this->assertNull($trail[0]['url']);
        $this->assertSame('Products', $trail[1]['label']);
        $this->assertNull($trail[1]['url']);
    }

    public function testHumanizeHandlesHyphensAndUnderscores(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $router = $container->get(RouterInterface::class);
        $requestStack = new RequestStack();

        $renderer = new BreadcrumbRenderer(
            $requestStack,
            $router,
            new AdminRuntime([]),
        );

        $request = Request::create('/admin/blog-posts/featured_items');
        $requestStack->push($request);

        $trail = $renderer->build();

        $labels = array_column($trail, 'label');
        $this->assertContains('Blog Posts', $labels);
        $this->assertContains('Featured Items', $labels);
    }

    public function testPathBasedUrlsDoNotDoublePrefix(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $router = $container->get(RouterInterface::class);
        $requestStack = new RequestStack();

        $renderer = new BreadcrumbRenderer(
            $requestStack,
            $router,
            new AdminRuntime(['route_prefix' => '/admin']),
        );

        $request = Request::create('/admin/users/123');
        $requestStack->push($request);

        $trail = $renderer->build();

        $this->assertCount(3, $trail);
        $this->assertSame('/admin', $trail[0]['url']);
        $this->assertSame('/admin/users', $trail[1]['url']);
        $this->assertNull($trail[2]['url']);
    }

    public function testPathBasedUrlsHandleCustomPrefix(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $router = $container->get(RouterInterface::class);
        $requestStack = new RequestStack();

        $renderer = new BreadcrumbRenderer(
            $requestStack,
            $router,
            new AdminRuntime(['route_prefix' => '/beacon']),
        );

        $request = Request::create('/beacon/users/123');
        $requestStack->push($request);

        $trail = $renderer->build();

        $this->assertSame('/beacon', $trail[0]['url']);
        $this->assertSame('/beacon/users', $trail[1]['url']);
    }
}
