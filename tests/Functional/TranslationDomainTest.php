<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Functional;

use Devgeek\BeaconAdmin\Tests\Fixtures\TestApp\TranslationTestKernel;

final class TranslationDomainTest extends BeaconWebTestCase
{
    protected static function getKernelClass(): string
    {
        return TranslationTestKernel::class;
    }

    public function testFrenchDashboardTitleIsTranslated(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin');

        $this->assertResponseIsSuccessful();

        $content = $client->getResponse()->getContent() ?? '';
        $this->assertStringContainsString('Tableau de bord', $content);
    }

    public function testFrenchWelcomeBackIsTranslated(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin');

        $this->assertResponseIsSuccessful();

        $content = $client->getResponse()->getContent() ?? '';
        $this->assertStringContainsString('Bon retour', $content);
    }

    public function testFrenchNoWidgetsMessageIsTranslated(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin');

        $this->assertResponseIsSuccessful();

        $content = $client->getResponse()->getContent() ?? '';
        $this->assertStringContainsString('Aucun widget configuré.', $content);
    }

    public function testFrenchBreadcrumbAriaLabelIsTranslated(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/admin');

        $this->assertResponseIsSuccessful();

        $ariaLabels = $crawler->filter('nav')->each(static function ($node) {
            return $node->attr('aria-label');
        });

        $this->assertContains("Fil d'Ariane", $ariaLabels);
    }

    public function testFrenchSidebarToggleAriaLabelIsTranslated(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/admin');

        $this->assertResponseIsSuccessful();

        $toggleAria = $crawler->filter('.beacon-sidebar-toggle')->attr('aria-label');
        $this->assertNotNull($toggleAria);
        $this->assertStringContainsString('menu latéral', $toggleAria);
    }

    public function testFrenchNavigationAriaLabelsAreTranslated(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/admin');

        $this->assertResponseIsSuccessful();

        $selectAria = $crawler->filter('.beacon-theme-select')->attr('aria-label');
        $this->assertNotNull($selectAria);
        $this->assertStringContainsString('thème', $selectAria);

        $darkModeAria = $crawler->filter('.beacon-theme-toggle')->attr('aria-label');
        $this->assertNotNull($darkModeAria);
        $this->assertStringContainsString('mode sombre', $darkModeAria);

        $mobileAria = $crawler->filter('.beacon-header-mobile-toggle')->attr('aria-label');
        $this->assertNotNull($mobileAria);
        $this->assertStringContainsString('navigation', $mobileAria);
    }
}
