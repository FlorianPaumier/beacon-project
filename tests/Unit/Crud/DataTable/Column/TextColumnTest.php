<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Crud\DataTable\Column;

use Devgeek\BeaconAdmin\Crud\DataTable\Column\TextColumn;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class TextColumnTest extends TestCase
{
    #[Test]
    public function itCreatesViaMake(): void
    {
        $column = TextColumn::make('name');

        $this->assertInstanceOf(TextColumn::class, $column);
    }

    #[Test]
    public function itDefaultsLimitToNull(): void
    {
        $column = TextColumn::make('name');

        $this->assertNull($column->getLimit());
    }

    #[Test]
    public function itSetsLimitFluently(): void
    {
        $column = TextColumn::make('name')->limit(100);

        $this->assertSame(100, $column->getLimit());
    }

    #[Test]
    public function itResetsLimitToNull(): void
    {
        $column = TextColumn::make('name')->limit(100)->limit(null);

        $this->assertNull($column->getLimit());
    }

    #[Test]
    public function itResolvesCorrectTemplate(): void
    {
        $column = TextColumn::make('name');

        $this->assertStringContainsString('text.html.twig', $column->getTemplate());
    }
}
