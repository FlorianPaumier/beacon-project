<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Form\Component;

use Devgeek\BeaconAdmin\Form\Component\Date;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class DateTest extends TestCase
{
    #[Test]
    public function itCreatesViaMake(): void
    {
        $input = Date::make();

        $this->assertInstanceOf(Date::class, $input);
    }

    #[Test]
    public function itSetsNameFluently(): void
    {
        $input = Date::make()->name('start_date');

        $this->assertSame('start_date', $input->getName());
    }

    #[Test]
    public function itSetsLabelFluently(): void
    {
        $input = Date::make()->name('start_date')->label('Start Date');

        $this->assertSame('Start Date', $input->getLabel());
    }

    #[Test]
    public function itEvaluatesClosureLabel(): void
    {
        $input = Date::make()->name('start_date')->label(fn () => 'Begin Date');

        $this->assertSame('Begin Date', $input->getLabel());
    }

    #[Test]
    public function itDefaultsRequiredToFalse(): void
    {
        $input = Date::make()->name('start_date');

        $this->assertFalse($input->isRequired());
    }

    #[Test]
    public function itSetsRequiredFluently(): void
    {
        $input = Date::make()->name('start_date')->required();

        $this->assertTrue($input->isRequired());
    }

    #[Test]
    public function itEvaluatesClosureRequired(): void
    {
        $input = Date::make()->name('start_date')->required(fn () => true);

        $this->assertTrue($input->isRequired());
    }

    #[Test]
    public function itReturnsNullMinByDefault(): void
    {
        $input = Date::make()->name('start_date');

        $this->assertNull($input->getMin());
    }

    #[Test]
    public function itSetsMinFluently(): void
    {
        $input = Date::make()->name('start_date')->min('2024-01-01');

        $this->assertSame('2024-01-01', $input->getMin());
    }

    #[Test]
    public function itEvaluatesClosureMin(): void
    {
        $input = Date::make()->name('start_date')->min(fn () => '2024-06-01');

        $this->assertSame('2024-06-01', $input->getMin());
    }

    #[Test]
    public function itReturnsNullMaxByDefault(): void
    {
        $input = Date::make()->name('start_date');

        $this->assertNull($input->getMax());
    }

    #[Test]
    public function itSetsMaxFluently(): void
    {
        $input = Date::make()->name('start_date')->max('2024-12-31');

        $this->assertSame('2024-12-31', $input->getMax());
    }

    #[Test]
    public function itEvaluatesClosureMax(): void
    {
        $input = Date::make()->name('start_date')->max(fn () => '2025-01-01');

        $this->assertSame('2025-01-01', $input->getMax());
    }

    #[Test]
    public function itChainsAllSetters(): void
    {
        $input = Date::make()
            ->name('end_date')
            ->label('End Date')
            ->required()
            ->min('2024-01-01')
            ->max('2024-12-31');

        $this->assertSame('end_date', $input->getName());
        $this->assertSame('End Date', $input->getLabel());
        $this->assertTrue($input->isRequired());
        $this->assertSame('2024-01-01', $input->getMin());
        $this->assertSame('2024-12-31', $input->getMax());
    }
}
