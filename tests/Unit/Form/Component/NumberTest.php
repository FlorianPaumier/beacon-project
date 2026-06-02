<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Form\Component;

use Devgeek\BeaconAdmin\Form\Component\Number;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class NumberTest extends TestCase
{
    #[Test]
    public function itCreatesViaMake(): void
    {
        $input = Number::make();

        $this->assertInstanceOf(Number::class, $input);
    }

    #[Test]
    public function itSetsNameFluently(): void
    {
        $input = Number::make()->name('price');

        $this->assertSame('price', $input->getName());
    }

    #[Test]
    public function itSetsLabelFluently(): void
    {
        $input = Number::make()->name('price')->label('Price');

        $this->assertSame('Price', $input->getLabel());
    }

    #[Test]
    public function itEvaluatesClosureLabel(): void
    {
        $input = Number::make()->name('price')->label(fn () => 'Product Price');

        $this->assertSame('Product Price', $input->getLabel());
    }

    #[Test]
    public function itDefaultsRequiredToFalse(): void
    {
        $input = Number::make()->name('price');

        $this->assertFalse($input->isRequired());
    }

    #[Test]
    public function itSetsRequiredFluently(): void
    {
        $input = Number::make()->name('price')->required();

        $this->assertTrue($input->isRequired());
    }

    #[Test]
    public function itEvaluatesClosureRequired(): void
    {
        $input = Number::make()->name('price')->required(fn () => true);

        $this->assertTrue($input->isRequired());
    }

    #[Test]
    public function itReturnsNullPlaceholderByDefault(): void
    {
        $input = Number::make()->name('price');

        $this->assertNull($input->getPlaceholder());
    }

    #[Test]
    public function itSetsPlaceholderFluently(): void
    {
        $input = Number::make()->name('price')->placeholder('0.00');

        $this->assertSame('0.00', $input->getPlaceholder());
    }

    #[Test]
    public function itEvaluatesClosurePlaceholder(): void
    {
        $input = Number::make()->name('price')->placeholder(fn () => 'Enter amount');

        $this->assertSame('Enter amount', $input->getPlaceholder());
    }

    #[Test]
    public function itReturnsNullMinByDefault(): void
    {
        $input = Number::make()->name('price');

        $this->assertNull($input->getMin());
    }

    #[Test]
    public function itSetsMinFluently(): void
    {
        $input = Number::make()->name('price')->min(0.0);

        $this->assertSame(0.0, $input->getMin());
    }

    #[Test]
    public function itEvaluatesClosureMin(): void
    {
        $input = Number::make()->name('price')->min(fn () => 1.0);

        $this->assertSame(1.0, $input->getMin());
    }

    #[Test]
    public function itReturnsNullMaxByDefault(): void
    {
        $input = Number::make()->name('price');

        $this->assertNull($input->getMax());
    }

    #[Test]
    public function itSetsMaxFluently(): void
    {
        $input = Number::make()->name('price')->max(1000.0);

        $this->assertSame(1000.0, $input->getMax());
    }

    #[Test]
    public function itEvaluatesClosureMax(): void
    {
        $input = Number::make()->name('price')->max(fn () => 500.0);

        $this->assertSame(500.0, $input->getMax());
    }

    #[Test]
    public function itReturnsNullStepByDefault(): void
    {
        $input = Number::make()->name('price');

        $this->assertNull($input->getStep());
    }

    #[Test]
    public function itSetsStepFluently(): void
    {
        $input = Number::make()->name('price')->step(0.5);

        $this->assertSame(0.5, $input->getStep());
    }

    #[Test]
    public function itEvaluatesClosureStep(): void
    {
        $input = Number::make()->name('price')->step(fn () => 0.1);

        $this->assertSame(0.1, $input->getStep());
    }

    #[Test]
    public function itChainsAllSetters(): void
    {
        $input = Number::make()
            ->name('quantity')
            ->label('Quantity')
            ->required()
            ->placeholder('0')
            ->min(1.0)
            ->max(100.0)
            ->step(1.0);

        $this->assertSame('quantity', $input->getName());
        $this->assertSame('Quantity', $input->getLabel());
        $this->assertTrue($input->isRequired());
        $this->assertSame('0', $input->getPlaceholder());
        $this->assertSame(1.0, $input->getMin());
        $this->assertSame(100.0, $input->getMax());
        $this->assertSame(1.0, $input->getStep());
    }
}
