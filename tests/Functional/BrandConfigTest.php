<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Functional;

use Devgeek\BeaconAdmin\Tests\Fixtures\TestApp\BrandTestKernel;

final class BrandConfigTest extends BeaconWebTestCase
{
    protected static function getKernelClass(): string
    {
        return BrandTestKernel::class;
    }

    public function testBrandNameIsRenderedInDashboard(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/admin');

        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString('My Custom App', (string) $client->getResponse()->getContent());
    }

    public function testBrandNameIsRenderedInSidebar(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/admin');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('.beacon-sidebar__brand', 'My Custom App');
    }

    public function testBrandPrimaryColorCssVariableIsInjected(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin');

        $this->assertResponseIsSuccessful();

        $content = (string) $client->getResponse()->getContent();
        $this->assertStringContainsString('--beacon-brand-primary: #ff5722', $content);
    }

    public function testBrandAccentColorCssVariableIsInjected(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin');

        $this->assertResponseIsSuccessful();

        $content = (string) $client->getResponse()->getContent();
        $this->assertStringContainsString('--beacon-brand-accent: #03a9f4', $content);
    }

    public function testBrandStyleBlockIsPresentInHead(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin');

        $this->assertResponseIsSuccessful();

        $content = (string) $client->getResponse()->getContent();
        $this->assertMatchesRegularExpression(
            '#<style>.*--beacon-brand-primary:.*</style>#s',
            $content,
        );
    }

    public function testBrandConfigIsExposedInContainerParameters(): void
    {
        $client = static::createClient();
        $container = $client->getContainer();

        $this->assertSame('My Custom App', $container->getParameter('beacon_admin.brand.name'));
        $this->assertSame('#ff5722', $container->getParameter('beacon_admin.brand.primary_color'));
        $this->assertSame('#03a9f4', $container->getParameter('beacon_admin.brand.accent_color'));
    }
}
