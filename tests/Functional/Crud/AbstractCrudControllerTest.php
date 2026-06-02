<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Functional\Crud;

use Devgeek\BeaconAdmin\Controller\AbstractCrudController;
use Devgeek\BeaconAdmin\Crud\CrudConfig;
use Devgeek\BeaconAdmin\Tests\Fixtures\TestApp\Entity\TestEntity;
use Devgeek\BeaconAdmin\Tests\Fixtures\TestApp\TestKernel;
use Devgeek\BeaconAdmin\Tests\Functional\BeaconWebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

final class AbstractCrudControllerTest extends BeaconWebTestCase
{
    protected static function getKernelClass(): string
    {
        return TestKernel::class;
    }

    public function testConcreteCrudControllerConfiguresCorrectly(): void
    {
        $controller = new TestCrudController();

        $config = $controller->getCrudConfig();

        $this->assertSame(TestEntity::class, $config->getEntityClass());
        $this->assertSame(['id', 'name', 'email'], $config->getFields());
        $this->assertSame(10, $config->getPageSize());
    }

    public function testCrudConfigIsFluent(): void
    {
        $config = CrudConfig::make()
            ->entityClass(TestEntity::class)
            ->fields(['id', 'name'])
            ->pageSize(30);

        $this->assertSame(TestEntity::class, $config->getEntityClass());
        $this->assertSame(30, $config->getPageSize());
    }

    public function testBulkDeleteRouteExists(): void
    {
        $refl = new \ReflectionMethod(AbstractCrudController::class, 'bulkDelete');
        $attributes = $refl->getAttributes(Route::class);

        $this->assertCount(1, $attributes);
        $route = $attributes[0]->newInstance();
        $this->assertSame('/bulk', $route->path);
        $this->assertSame(['POST'], $route->methods);
        $this->assertSame('bulk', $route->name);
    }

    public function testGetListRouteStripsBulkSuffix(): void
    {
        $controller = new TestCrudController();
        $request = new Request();
        $request->attributes->set('_route', 'admin_user_bulk');

        $refl = new \ReflectionMethod($controller, 'getListRoute');
        $result = $refl->invoke($controller, $request);

        $this->assertSame('admin_user_list', $result);
    }

    public function testGetListRouteStripsDeleteSuffix(): void
    {
        $controller = new TestCrudController();
        $request = new Request();
        $request->attributes->set('_route', 'admin_user_delete');

        $refl = new \ReflectionMethod($controller, 'getListRoute');
        $result = $refl->invoke($controller, $request);

        $this->assertSame('admin_user_list', $result);
    }

    public function testGetListRouteStripsToggleSuffix(): void
    {
        $controller = new TestCrudController();
        $request = new Request();
        $request->attributes->set('_route', 'admin_user_toggle');

        $refl = new \ReflectionMethod($controller, 'getListRoute');
        $result = $refl->invoke($controller, $request);

        $this->assertSame('admin_user_list', $result);
    }

    public function testToggleBooleanRouteExists(): void
    {
        $refl = new \ReflectionMethod(AbstractCrudController::class, 'toggleBoolean');
        $attributes = $refl->getAttributes(Route::class);

        $this->assertCount(1, $attributes);
        $route = $attributes[0]->newInstance();
        $this->assertSame('/{id}/toggle/{field}', $route->path);
        $this->assertSame(['POST'], $route->methods);
        $this->assertSame('toggle', $route->name);
    }

    public function testToggleBooleanRejectsMissingField(): void
    {
        $controller = new TestCrudController();

        $refl = new \ReflectionClass(TestEntity::class);
        $this->assertTrue($refl->hasProperty('active'));
        $this->assertFalse($refl->hasProperty('nonexistent'));
    }
}

/** @internal */
#[AsController]
final class TestCrudController extends AbstractCrudController
{
    protected function configureCrud(CrudConfig $config): void
    {
        $config
            ->fields(['id', 'name', 'email'])
            ->sortableFields(['id', 'name'])
            ->searchableFields(['name', 'email'])
            ->pageSize(10);
    }

    protected function getEntityClass(): string
    {
        return TestEntity::class;
    }
}
