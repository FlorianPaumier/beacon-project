<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Crud\DataTable\Column;

use Devgeek\BeaconAdmin\Crud\DataTable\Column\BooleanColumn;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class BooleanColumnTest extends TestCase
{
    #[Test]
    public function itCreatesViaMake(): void
    {
        $column = BooleanColumn::make('is_active');

        $this->assertInstanceOf(BooleanColumn::class, $column);
        $this->assertSame('is_active', $column->getName());
    }

    #[Test]
    public function itResolvesCorrectTemplate(): void
    {
        $column = BooleanColumn::make('active');

        $this->assertStringContainsString('boolean.html.twig', $column->getTemplate());
    }

    #[Test]
    public function itUsesInheritedLabelBehavior(): void
    {
        $column = BooleanColumn::make('is_active');

        $this->assertSame('Is active', $column->getLabel());
    }

    #[Test]
    public function itDefaultsToggleableToFalse(): void
    {
        $column = BooleanColumn::make('active');

        $this->assertFalse($column->isToggleable());
    }

    #[Test]
    public function itSetsToggleable(): void
    {
        $column = BooleanColumn::make('active')->toggleable();

        $this->assertTrue($column->isToggleable());
    }

    #[Test]
    public function itChainsToggleableWithOtherSetters(): void
    {
        $column = BooleanColumn::make('active')
            ->trueLabel('On')
            ->falseLabel('Off')
            ->toggleable();

        $this->assertTrue($column->isToggleable());
        $this->assertSame('On', $column->getTrueLabel());
        $this->assertSame('Off', $column->getFalseLabel());
    }
}
