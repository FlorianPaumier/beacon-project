<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Crud\DataTable;

use Devgeek\BeaconAdmin\Crud\DataTable\Column\TextColumn;
use Devgeek\BeaconAdmin\Crud\DataTable\DataTable;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class DataTableTest extends TestCase
{
    #[Test]
    public function itCreatesViaMake(): void
    {
        $table = DataTable::make();

        $this->assertInstanceOf(DataTable::class, $table);
    }

    #[Test]
    public function itDefaultsColumnsToEmpty(): void
    {
        $table = DataTable::make();

        $this->assertSame([], $table->getColumns());
    }

    #[Test]
    public function itSetsColumns(): void
    {
        $column = TextColumn::make('name');
        $table = DataTable::make()->columns([$column]);

        $this->assertCount(1, $table->getColumns());
        $this->assertSame($column, $table->getColumns()[0]);
    }

    #[Test]
    public function itAddsColumn(): void
    {
        $column = TextColumn::make('email');
        $table = DataTable::make()->addColumn($column);

        $this->assertCount(1, $table->getColumns());
        $this->assertSame($column, $table->getColumns()[0]);
    }

    #[Test]
    public function itAddsMultipleColumns(): void
    {
        $col1 = TextColumn::make('name');
        $col2 = TextColumn::make('email');
        $table = DataTable::make()->addColumn($col1)->addColumn($col2);

        $this->assertCount(2, $table->getColumns());
    }

    #[Test]
    public function itDefaultsSortableToTrue(): void
    {
        $table = DataTable::make();

        $this->assertTrue($table->isSortable());
    }

    #[Test]
    public function itSetsSortable(): void
    {
        $table = DataTable::make()->sortable(false);

        $this->assertFalse($table->isSortable());
    }

    #[Test]
    public function itDefaultsSearchableToTrue(): void
    {
        $table = DataTable::make();

        $this->assertTrue($table->isSearchable());
    }

    #[Test]
    public function itSetsSearchable(): void
    {
        $table = DataTable::make()->searchable(false);

        $this->assertFalse($table->isSearchable());
    }

    #[Test]
    public function itDefaultsPageSizeTo25(): void
    {
        $table = DataTable::make();

        $this->assertSame(25, $table->getPageSize());
    }

    #[Test]
    public function itSetsPageSize(): void
    {
        $table = DataTable::make()->pageSize(10);

        $this->assertSame(10, $table->getPageSize());
    }

    #[Test]
    public function itChainsAllSetters(): void
    {
        $col1 = TextColumn::make('name');
        $col2 = TextColumn::make('email');
        $table = DataTable::make()
            ->columns([$col1])
            ->addColumn($col2)
            ->sortable(true)
            ->searchable(false)
            ->pageSize(50);

        $this->assertCount(2, $table->getColumns());
        $this->assertTrue($table->isSortable());
        $this->assertFalse($table->isSearchable());
        $this->assertSame(50, $table->getPageSize());
    }
}
