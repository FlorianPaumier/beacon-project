<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Crud\DataTable\Column;

use Devgeek\BeaconAdmin\Crud\DataTable\Column\NumberColumn;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class NumberColumnTest extends TestCase
{
    #[Test]
    public function itCreatesViaMake(): void
    {
        $column = NumberColumn::make('price');

        $this->assertSame(0, $column->getDecimals());
    }

    #[Test]
    public function itHasDefaultFormatting(): void
    {
        $column = NumberColumn::make('price');

        $this->assertSame(0, $column->getDecimals());
        $this->assertSame('.', $column->getDecimalSeparator());
        $this->assertSame(',', $column->getThousandsSeparator());
    }

    #[Test]
    public function itSetsDecimalsFluently(): void
    {
        $column = NumberColumn::make('price')->decimals(2);

        $this->assertSame(2, $column->getDecimals());
    }

    #[Test]
    public function itSetsDecimalSeparator(): void
    {
        $column = NumberColumn::make('price')->decimalSeparator(',');

        $this->assertSame(',', $column->getDecimalSeparator());
    }

    #[Test]
    public function itSetsThousandsSeparator(): void
    {
        $column = NumberColumn::make('price')->thousandsSeparator('.');

        $this->assertSame('.', $column->getThousandsSeparator());
    }

    #[Test]
    public function itFormatsValueWithDecimals(): void
    {
        $column = NumberColumn::make('price')->decimals(2);

        $this->assertSame('1,234.56', $column->formatValue(1234.56));
    }

    #[Test]
    public function itFormatsValueWithCustomSeparators(): void
    {
        $column = NumberColumn::make('price')
            ->decimals(2)
            ->decimalSeparator(',')
            ->thousandsSeparator('.');

        $this->assertSame('1.234,56', $column->formatValue(1234.56));
    }

    #[Test]
    public function itReturnsEmptyStringForNullValue(): void
    {
        $column = NumberColumn::make('price');

        $this->assertSame('', $column->formatValue(null));
    }

    #[Test]
    public function itResolvesCorrectTemplate(): void
    {
        $column = NumberColumn::make('price');

        $this->assertStringContainsString('number.html.twig', $column->getTemplate());
    }
}
