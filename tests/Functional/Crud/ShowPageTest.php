<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Functional\Crud;

use Devgeek\BeaconAdmin\Controller\AbstractCrudController;
use Devgeek\BeaconAdmin\Crud\CrudConfig;
use Devgeek\BeaconAdmin\Tests\Fixtures\TestApp\Entity\TestEntity;
use Devgeek\BeaconAdmin\Tests\Fixtures\TestApp\TestKernel;
use Devgeek\BeaconAdmin\Tests\Functional\BeaconWebTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class ShowPageTest extends BeaconWebTestCase
{
    private KernelBrowser $client;
    private int $entityId;

    protected static function getKernelClass(): string
    {
        return TestKernel::class;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();
        $container = $this->client->getContainer();
        $em = $container->get('doctrine.orm.entity_manager');
        \assert($em instanceof EntityManagerInterface);

        $schemaTool = new SchemaTool($em);
        $schemaTool->createSchema([
            $em->getClassMetadata(TestEntity::class),
        ]);

        $entity = new TestEntity();
        $entity->setName('John Doe');
        $entity->setEmail('john@example.com');
        $entity->setActive(true);
        $em->persist($entity);
        $em->flush();
        $em->clear();

        $this->entityId = (int) $entity->getId();
    }

    public function testShowRouteIsRegistered(): void
    {
        $refl = new \ReflectionMethod(AbstractCrudController::class, 'show');
        $attributes = $refl->getAttributes(Route::class);

        $this->assertCount(1, $attributes);
        $route = $attributes[0]->newInstance();
        $this->assertSame('/{id}', $route->path);
        $this->assertSame(['GET'], $route->methods);
        $this->assertSame('show', $route->name);
    }

    public function testShowPageRendersForValidEntity(): void
    {
        $this->client->request('GET', '/admin/show-test/'.$this->entityId);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('body', 'John Doe');
        $this->assertSelectorTextContains('body', 'john@example.com');
    }

    public function testShowPageContainsEditLink(): void
    {
        $this->client->request('GET', '/admin/show-test/'.$this->entityId);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('a[href*="/edit"]');
    }

    public function testShowPageContainsBackLink(): void
    {
        $this->client->request('GET', '/admin/show-test/'.$this->entityId);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('a[href*="/admin/show-test"]');
    }

    public function testShowPageReturns404ForInvalidId(): void
    {
        $this->client->request('GET', '/admin/show-test/99999');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testGetListRouteStripsShowSuffix(): void
    {
        $controller = new ShowPageTestCrudController();
        $request = new Request();
        $request->attributes->set('_route', 'show_test_show');

        $refl = new \ReflectionMethod($controller, 'getListRoute');
        $result = $refl->invoke($controller, $request);

        $this->assertSame('show_test_list', $result);
    }

    public function testGetShowFieldsFallsBackToColumnNames(): void
    {
        $controller = new ShowPageTestCrudController();
        $config = $controller->getCrudConfig();

        $this->assertSame(['name', 'email', 'active'], $config->getShowFields());
    }

    public function testGetShowFieldsReturnsExplicitlyConfiguredFields(): void
    {
        $config = CrudConfig::make()
            ->entityClass(TestEntity::class)
            ->showFields(['id', 'name']);

        $this->assertSame(['id', 'name'], $config->getShowFields());
    }
}
