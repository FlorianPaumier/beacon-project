<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Functional\Crud;

use Devgeek\BeaconAdmin\Controller\AbstractCrudController;
use Devgeek\BeaconAdmin\Crud\CrudConfig;
use Devgeek\BeaconAdmin\Tests\Fixtures\TestApp\Entity\TestEntity;
use Devgeek\BeaconAdmin\Tests\Fixtures\TestApp\TestKernel;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\Attribute\AsController;

final class AbstractCrudControllerTest extends WebTestCase
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
