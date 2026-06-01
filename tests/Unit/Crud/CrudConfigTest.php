<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Crud;

use Devgeek\BeaconAdmin\Crud\CrudConfig;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class CrudConfigTest extends TestCase
{
    #[Test]
    public function itCreatesViaMake(): void
    {
        $config = CrudConfig::make();

        $this->assertInstanceOf(CrudConfig::class, $config);
    }

    #[Test]
    public function itDefaultPageSizeIs25(): void
    {
        $config = CrudConfig::make();

        $this->assertSame(25, $config->getPageSize());
    }

    #[Test]
    public function itSetsEntityClass(): void
    {
        $config = CrudConfig::make()->entityClass('App\Entity\User');

        $this->assertSame('App\Entity\User', $config->getEntityClass());
    }

    #[Test]
    public function itSetsFields(): void
    {
        $config = CrudConfig::make()->fields(['id', 'name', 'email']);

        $this->assertSame(['id', 'name', 'email'], $config->getFields());
    }

    #[Test]
    public function itSetsSortableFields(): void
    {
        $config = CrudConfig::make()->sortableFields(['name', 'created_at']);

        $this->assertSame(['name', 'created_at'], $config->getSortableFields());
    }

    #[Test]
    public function itSetsSearchableFields(): void
    {
        $config = CrudConfig::make()->searchableFields(['name', 'email']);

        $this->assertSame(['name', 'email'], $config->getSearchableFields());
    }

    #[Test]
    public function itSetsPageSize(): void
    {
        $config = CrudConfig::make()->pageSize(10);

        $this->assertSame(10, $config->getPageSize());
    }

    #[Test]
    public function itChainsAllSetters(): void
    {
        $config = CrudConfig::make()
            ->entityClass('App\Entity\User')
            ->fields(['id', 'name'])
            ->sortableFields(['name'])
            ->searchableFields(['name'])
            ->pageSize(50);

        $this->assertSame('App\Entity\User', $config->getEntityClass());
        $this->assertSame(['id', 'name'], $config->getFields());
        $this->assertSame(['name'], $config->getSortableFields());
        $this->assertSame(['name'], $config->getSearchableFields());
        $this->assertSame(50, $config->getPageSize());
    }

    #[Test]
    public function itDefaultsFieldsToEmpty(): void
    {
        $config = CrudConfig::make();

        $this->assertSame([], $config->getFields());
        $this->assertSame([], $config->getSortableFields());
        $this->assertSame([], $config->getSearchableFields());
    }
}
