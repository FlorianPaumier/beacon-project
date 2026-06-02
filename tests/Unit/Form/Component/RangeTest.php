<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Form\Component;

use Devgeek\BeaconAdmin\Form\Component\Range;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class RangeTest extends TestCase
{
    #[Test]
    public function itCreatesViaMake(): void
    {
        $input = Range::make()->name('test');

        $this->assertSame('test', $input->getName());
    }

    #[Test]
    public function itSetsNameFluently(): void
    {
        $input = Range::make()->name('volume');

        $this->assertSame('volume', $input->getName());
    }

    #[Test]
    public function itSetsLabelFluently(): void
    {
        $input = Range::make()->name('volume')->label('Volume');

        $this->assertSame('Volume', $input->getLabel());
    }

    #[Test]
    public function itEvaluatesClosureLabel(): void
    {
        $input = Range::make()->name('volume')->label(fn () => 'Sound Level');

        $this->assertSame('Sound Level', $input->getLabel());
    }

    #[Test]
    public function itDefaultsMinToZero(): void
    {
        $input = Range::make()->name('volume');

        $this->assertSame(0.0, $input->getMin());
    }

    #[Test]
    public function itSetsMinFluently(): void
    {
        $input = Range::make()->name('volume')->min(10.0);

        $this->assertSame(10.0, $input->getMin());
    }

    #[Test]
    public function itEvaluatesClosureMin(): void
    {
        $input = Range::make()->name('volume')->min(fn () => 5.0);

        $this->assertSame(5.0, $input->getMin());
    }

    #[Test]
    public function itDefaultsMaxToOneHundred(): void
    {
        $input = Range::make()->name('volume');

        $this->assertSame(100.0, $input->getMax());
    }

    #[Test]
    public function itSetsMaxFluently(): void
    {
        $input = Range::make()->name('volume')->max(200.0);

        $this->assertSame(200.0, $input->getMax());
    }

    #[Test]
    public function itEvaluatesClosureMax(): void
    {
        $input = Range::make()->name('volume')->max(fn () => 150.0);

        $this->assertSame(150.0, $input->getMax());
    }

    #[Test]
    public function itDefaultsStepToOne(): void
    {
        $input = Range::make()->name('volume');

        $this->assertSame(1.0, $input->getStep());
    }

    #[Test]
    public function itSetsStepFluently(): void
    {
        $input = Range::make()->name('volume')->step(5.0);

        $this->assertSame(5.0, $input->getStep());
    }

    #[Test]
    public function itEvaluatesClosureStep(): void
    {
        $input = Range::make()->name('volume')->step(fn () => 2.0);

        $this->assertSame(2.0, $input->getStep());
    }

    #[Test]
    public function itDefaultsShowValueToTrue(): void
    {
        $input = Range::make()->name('volume');

        $this->assertTrue($input->isShowValue());
    }

    #[Test]
    public function itSetsShowValueFluently(): void
    {
        $input = Range::make()->name('volume')->showValue(false);

        $this->assertFalse($input->isShowValue());
    }

    #[Test]
    public function itEvaluatesClosureShowValue(): void
    {
        $input = Range::make()->name('volume')->showValue(fn () => true);

        $this->assertTrue($input->isShowValue());
    }

    #[Test]
    public function itChainsAllSetters(): void
    {
        $input = Range::make()
            ->name('brightness')
            ->label('Brightness')
            ->min(0.0)
            ->max(255.0)
            ->step(5.0)
            ->showValue(true);

        $this->assertSame('brightness', $input->getName());
        $this->assertSame('Brightness', $input->getLabel());
        $this->assertSame(0.0, $input->getMin());
        $this->assertSame(255.0, $input->getMax());
        $this->assertSame(5.0, $input->getStep());
        $this->assertTrue($input->isShowValue());
    }
}
