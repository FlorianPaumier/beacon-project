<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Crud\DataTable\Column;

use Devgeek\BeaconAdmin\Crud\DataTable\Column\DateTimeColumn;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class DateTimeColumnTest extends TestCase
{
    #[Test]
    public function itCreatesViaMake(): void
    {
        $column = DateTimeColumn::make('created_at');

        $this->assertInstanceOf(DateTimeColumn::class, $column);
        $this->assertSame('created_at', $column->getName());
    }

    #[Test]
    public function itHasDefaultFormat(): void
    {
        $column = DateTimeColumn::make('created_at');

        $this->assertSame('Y-m-d H:i', $column->getFormat());
    }

    #[Test]
    public function itSetsFormatFluently(): void
    {
        $column = DateTimeColumn::make('created_at')->format('d/m/Y');

        $this->assertSame('d/m/Y', $column->getFormat());
    }

    #[Test]
    public function itResolvesCorrectTemplate(): void
    {
        $column = DateTimeColumn::make('created_at');

        $this->assertStringContainsString('date.html.twig', $column->getTemplate());
    }
}
