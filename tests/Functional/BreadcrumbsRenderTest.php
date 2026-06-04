<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Functional;

use Devgeek\BeaconAdmin\Tests\Fixtures\TestApp\TestKernel;

final class BreadcrumbsRenderTest extends BeaconWebTestCase
{
    protected static function getKernelClass(): string
    {
        return TestKernel::class;
    }

    public function testBreadcrumbNavAppearsOnDashboard(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/admin');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('nav[aria-label="Breadcrumb"]');
    }

    public function testBreadcrumbHasOrderedList(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/admin');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('nav[aria-label="Breadcrumb"] ol.beacon-breadcrumbs');
    }

    public function testLastBreadcrumbItemIsCurrentPage(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/admin');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('li.beacon-breadcrumb-item--current span[aria-current="page"]');
    }
}
