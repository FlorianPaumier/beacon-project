<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Crud\DataTable\Column;

use Devgeek\BeaconAdmin\Crud\DataTable\Column\Column;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ColumnTest extends TestCase
{
    #[Test]
    public function itCreatesViaMake(): void
    {
        $column = $this->createColumn('test_name');

        $this->assertInstanceOf(Column::class, $column);
    }

    #[Test]
    public function itAutoGeneratesLabelFromName(): void
    {
        $column = $this->createColumn('created_at');

        $this->assertSame('Created at', $column->getLabel());
    }

    #[Test]
    public function itSetsLabelFluently(): void
    {
        $column = $this->createColumn('name')->label('Custom Label');

        $this->assertSame('Custom Label', $column->getLabel());
    }

    #[Test]
    public function itReturnsName(): void
    {
        $column = $this->createColumn('email');

        $this->assertSame('email', $column->getName());
    }

    #[Test]
    public function itDefaultsSortableToFalse(): void
    {
        $column = $this->createColumn('name');

        $this->assertFalse($column->isSortable());
    }

    #[Test]
    public function itSetsSortable(): void
    {
        $column = $this->createColumn('name')->sortable();

        $this->assertTrue($column->isSortable());
    }

    #[Test]
    public function itSetsSortableToFalse(): void
    {
        $column = $this->createColumn('name')->sortable(true)->sortable(false);

        $this->assertFalse($column->isSortable());
    }

    #[Test]
    public function itResolvesTemplateFromClassName(): void
    {
        $class = get_class(new class extends Column {});
        $column = $class::make('test');

        $template = $column->getTemplate();

        $this->assertStringEndsWith('.html.twig', $template);
    }

    #[Test]
    public function itChainsAllSetters(): void
    {
        $column = $this->createColumn('title')
            ->label('Post Title')
            ->sortable(true);

        $this->assertSame('title', $column->getName());
        $this->assertSame('Post Title', $column->getLabel());
        $this->assertTrue($column->isSortable());
    }

    private function createColumn(string $name): Column
    {
        $class = get_class(new class extends Column {});

        return $class::make($name);
    }
}
