<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Functional;

use Devgeek\BeaconAdmin\Tests\Fixtures\TestApp\TestKernel;
use Symfony\Component\Security\Core\User\InMemoryUser;

final class EnglishTranslationTest extends BeaconWebTestCase
{
    protected static function getKernelClass(): string
    {
        return TestKernel::class;
    }

    public function testEnglishWelcomeBackIsRendered(): void
    {
        $client = static::createClient();
        $client->loginUser(new InMemoryUser('admin_user', 'admin_pass', ['ROLE_ADMIN']));
        $client->request('GET', '/admin');

        $this->assertResponseIsSuccessful();

        $content = (string) $client->getResponse()->getContent();
        $this->assertStringContainsString('Welcome back', $content);
    }

    public function testEnglishNoWidgetsMessageIsRendered(): void
    {
        $client = static::createClient();
        $client->loginUser(new InMemoryUser('admin_user', 'admin_pass', ['ROLE_ADMIN']));
        $client->request('GET', '/admin');

        $this->assertResponseIsSuccessful();

        $content = (string) $client->getResponse()->getContent();
        $this->assertStringContainsString('No widgets configured.', $content);
    }

    public function testEnglishBreadcrumbAriaLabelIsRendered(): void
    {
        $client = static::createClient();
        $client->loginUser(new InMemoryUser('admin_user', 'admin_pass', ['ROLE_ADMIN']));
        $crawler = $client->request('GET', '/admin');

        $this->assertResponseIsSuccessful();

        $ariaLabels = $crawler->filter('nav')->each(static function ($node) {
            return $node->attr('aria-label');
        });

        $this->assertContains('Breadcrumb', $ariaLabels);
    }
}
