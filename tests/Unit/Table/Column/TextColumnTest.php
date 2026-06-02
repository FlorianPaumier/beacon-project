<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Table\Column;

use Devgeek\BeaconAdmin\Table\Column\TextColumn;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class TextColumnTest extends TestCase
{
    #[Test]
    public function itCreatesViaMake(): void
    {
        $column = TextColumn::make();

        $this->assertInstanceOf(TextColumn::class, $column);
    }

    #[Test]
    public function itSetsNameFluently(): void
    {
        $column = TextColumn::make()->name('email');

        $this->assertSame('email', $column->getName());
    }

    #[Test]
    public function itSetsLabelFluently(): void
    {
        $column = TextColumn::make()->name('email')->label('Email Address');

        $this->assertSame('Email Address', $column->getLabel());
    }

    #[Test]
    public function itReturnsNullLabelByDefault(): void
    {
        $column = TextColumn::make()->name('email');

        $this->assertNull($column->getLabel());
    }

    #[Test]
    public function itEvaluatesClosureLabel(): void
    {
        $column = TextColumn::make()->name('email')->label(fn () => 'Dynamic');

        $this->assertSame('Dynamic', $column->getLabel());
    }

    #[Test]
    public function itDefaultsSortableToFalse(): void
    {
        $column = TextColumn::make()->name('name');

        $this->assertFalse($column->isSortable());
    }

    #[Test]
    public function itSetsSortableFluently(): void
    {
        $column = TextColumn::make()->name('name')->sortable();

        $this->assertTrue($column->isSortable());
    }

    #[Test]
    public function itSetsSortableFalse(): void
    {
        $column = TextColumn::make()->name('name')->sortable(false);

        $this->assertFalse($column->isSortable());
    }

    #[Test]
    public function itEvaluatesClosureSortable(): void
    {
        $column = TextColumn::make()->name('name')->sortable(fn () => true);

        $this->assertTrue($column->isSortable());
    }

    #[Test]
    public function itDefaultsSearchableToFalse(): void
    {
        $column = TextColumn::make()->name('name');

        $this->assertFalse($column->isSearchable());
    }

    #[Test]
    public function itSetsSearchableFluently(): void
    {
        $column = TextColumn::make()->name('name')->searchable();

        $this->assertTrue($column->isSearchable());
    }

    #[Test]
    public function itEvaluatesClosureSearchable(): void
    {
        $column = TextColumn::make()->name('name')->searchable(fn () => true);

        $this->assertTrue($column->isSearchable());
    }

    #[Test]
    public function itReturnsNullFormatByDefault(): void
    {
        $column = TextColumn::make()->name('name');

        $this->assertNull($column->getFormat());
    }

    #[Test]
    public function itSetsFormatFluently(): void
    {
        $formatter = fn (mixed $value) => strtoupper((string) $value);
        $column = TextColumn::make()->name('name')->format($formatter);

        $this->assertSame($formatter, $column->getFormat());
    }

    #[Test]
    public function itReturnsNullLimitByDefault(): void
    {
        $column = TextColumn::make()->name('description');

        $this->assertNull($column->getLimit());
    }

    #[Test]
    public function itSetsLimitFluently(): void
    {
        $column = TextColumn::make()->name('description')->limit(100);

        $this->assertSame(100, $column->getLimit());
    }

    #[Test]
    public function itEvaluatesClosureLimit(): void
    {
        $column = TextColumn::make()->name('description')->limit(fn () => 50);

        $this->assertSame(50, $column->getLimit());
    }

    #[Test]
    public function itChainsAllSetters(): void
    {
        $formatter = fn (mixed $v) => (string) $v;
        $column = TextColumn::make()
            ->name('title')
            ->label('Title')
            ->sortable()
            ->searchable()
            ->format($formatter)
            ->limit(200);

        $this->assertSame('title', $column->getName());
        $this->assertSame('Title', $column->getLabel());
        $this->assertTrue($column->isSortable());
        $this->assertTrue($column->isSearchable());
        $this->assertSame($formatter, $column->getFormat());
        $this->assertSame(200, $column->getLimit());
    }
}
