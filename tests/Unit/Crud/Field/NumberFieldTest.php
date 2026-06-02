<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Crud\Field;

use Devgeek\BeaconAdmin\Crud\Field\NumberField;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

final class NumberFieldTest extends TestCase
{
    #[Test]
    public function itCreatesViaMake(): void
    {
        $field = NumberField::make('price');

        $this->assertSame('price', $field->getName());
    }

    #[Test]
    public function itAutoGeneratesLabel(): void
    {
        $field = NumberField::make('unit_price');

        $this->assertSame('Unit price', $field->getLabel());
    }

    #[Test]
    public function itDefaultsMinToNull(): void
    {
        $field = NumberField::make('price');

        $this->assertNull($field->getMin());
    }

    #[Test]
    public function itSetsMin(): void
    {
        $field = NumberField::make('price')->min(0.0);

        $this->assertSame(0.0, $field->getMin());
    }

    #[Test]
    public function itDefaultsMaxToNull(): void
    {
        $field = NumberField::make('price');

        $this->assertNull($field->getMax());
    }

    #[Test]
    public function itSetsMax(): void
    {
        $field = NumberField::make('price')->max(1000.0);

        $this->assertSame(1000.0, $field->getMax());
    }

    #[Test]
    public function itDefaultsStepToNull(): void
    {
        $field = NumberField::make('price');

        $this->assertNull($field->getStep());
    }

    #[Test]
    public function itSetsStep(): void
    {
        $field = NumberField::make('price')->step(0.5);

        $this->assertSame(0.5, $field->getStep());
    }

    #[Test]
    public function itReturnsNumberType(): void
    {
        $field = NumberField::make('price');

        $this->assertSame(NumberType::class, $field->getFormType());
    }

    #[Test]
    public function itChainsAllSetters(): void
    {
        $field = NumberField::make('quantity')
            ->label('Quantity')
            ->required(true)
            ->min(1.0)
            ->max(100.0)
            ->step(1.0);

        $this->assertSame('quantity', $field->getName());
        $this->assertSame('Quantity', $field->getLabel());
        $this->assertTrue($field->isRequired());
        $this->assertSame(1.0, $field->getMin());
        $this->assertSame(100.0, $field->getMax());
        $this->assertSame(1.0, $field->getStep());
    }
}
