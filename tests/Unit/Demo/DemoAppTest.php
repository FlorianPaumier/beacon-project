<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Demo;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class DemoAppTest extends TestCase
{
    private string $demoDir;

    protected function setUp(): void
    {
        $this->demoDir = __DIR__.'/../../../demo';
    }

    #[Test]
    public function composerJsonExistsAndIsValid(): void
    {
        $path = $this->demoDir.'/composer.json';
        $this->assertFileExists($path);

        $data = json_decode(file_get_contents($path), true);
        $this->assertIsArray($data);
        $this->assertSame('devgeek/beacon-admin-demo', $data['name']);
        $this->assertArrayHasKey('require', $data);
        $this->assertArrayHasKey('autoload', $data);
    }

    #[Test]
    public function envFileExists(): void
    {
        $this->assertFileExists($this->demoDir.'/.env');
    }

    #[Test]
    public function entityFilesExistAndAreValidPhp(): void
    {
        $entities = ['Product', 'Category', 'User'];
        foreach ($entities as $entity) {
            $path = $this->demoDir."/src/Entity/{$entity}.php";
            $this->assertFileExists($path);

            $content = file_get_contents($path);
            $this->assertStringContainsString('namespace App\Entity;', $content);
            $this->assertStringContainsString("class {$entity}", $content);
        }
    }

    #[Test]
    public function beaconAdminConfigExists(): void
    {
        $this->assertFileExists($this->demoDir.'/config/packages/beacon_admin.yaml');
    }

    #[Test]
    public function controllerFilesExist(): void
    {
        $controllers = ['ProductCrudController', 'CategoryCrudController', 'UserCrudController'];
        foreach ($controllers as $ctrl) {
            $path = $this->demoDir."/src/Controller/Admin/{$ctrl}.php";
            $this->assertFileExists($path);
        }
    }

    #[Test]
    public function doctrineConfigExists(): void
    {
        $this->assertFileExists($this->demoDir.'/config/packages/doctrine.yaml');
    }

    #[Test]
    public function routesConfigExists(): void
    {
        $this->assertFileExists($this->demoDir.'/config/routes/beacon_admin.yaml');
    }

    #[Test]
    public function publicIndexExists(): void
    {
        $this->assertFileExists($this->demoDir.'/public/index.php');
    }
}
