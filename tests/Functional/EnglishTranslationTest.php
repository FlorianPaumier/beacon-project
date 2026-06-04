<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Functional;

use Devgeek\BeaconAdmin\Tests\Fixtures\TestApp\TestKernel;

final class EnglishTranslationTest extends BeaconWebTestCase
{
    protected static function getKernelClass(): string
    {
        return TestKernel::class;
    }

    public function testEnglishWelcomeBackIsRendered(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin');

        $this->assertResponseIsSuccessful();

        $content = $client->getResponse()->getContent() ?? '';
        $this->assertStringContainsString('Welcome back', $content);
    }

    public function testEnglishNoWidgetsMessageIsRendered(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin');

        $this->assertResponseIsSuccessful();

        $content = $client->getResponse()->getContent() ?? '';
        $this->assertStringContainsString('No widgets configured.', $content);
    }

    public function testEnglishBreadcrumbAriaLabelIsRendered(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/admin');

        $this->assertResponseIsSuccessful();

        $ariaLabels = $crawler->filter('nav')->each(static function ($node) {
            return $node->attr('aria-label');
        });

        $this->assertContains('Breadcrumb', $ariaLabels);
    }
}
