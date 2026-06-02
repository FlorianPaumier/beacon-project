<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Form\Component;

use Devgeek\BeaconAdmin\Form\Component\DateTime;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class DateTimeTest extends TestCase
{
    #[Test]
    public function itCreatesViaMake(): void
    {
        $input = DateTime::make();

        $this->assertNull($input->getLabel());
    }

    #[Test]
    public function itSetsNameFluently(): void
    {
        $input = DateTime::make()->name('created_at');

        $this->assertSame('created_at', $input->getName());
    }

    #[Test]
    public function itSetsLabelFluently(): void
    {
        $input = DateTime::make()->name('created_at')->label('Created At');

        $this->assertSame('Created At', $input->getLabel());
    }

    #[Test]
    public function itEvaluatesClosureLabel(): void
    {
        $input = DateTime::make()->name('created_at')->label(fn () => 'Creation Date');

        $this->assertSame('Creation Date', $input->getLabel());
    }

    #[Test]
    public function itDefaultsRequiredToFalse(): void
    {
        $input = DateTime::make()->name('created_at');

        $this->assertFalse($input->isRequired());
    }

    #[Test]
    public function itSetsRequiredFluently(): void
    {
        $input = DateTime::make()->name('created_at')->required();

        $this->assertTrue($input->isRequired());
    }

    #[Test]
    public function itEvaluatesClosureRequired(): void
    {
        $input = DateTime::make()->name('created_at')->required(fn () => true);

        $this->assertTrue($input->isRequired());
    }

    #[Test]
    public function itReturnsNullMinByDefault(): void
    {
        $input = DateTime::make()->name('created_at');

        $this->assertNull($input->getMin());
    }

    #[Test]
    public function itSetsMinFluently(): void
    {
        $input = DateTime::make()->name('created_at')->min('2024-01-01T00:00:00');

        $this->assertSame('2024-01-01T00:00:00', $input->getMin());
    }

    #[Test]
    public function itEvaluatesClosureMin(): void
    {
        $input = DateTime::make()->name('created_at')->min(fn () => '2024-06-01T00:00:00');

        $this->assertSame('2024-06-01T00:00:00', $input->getMin());
    }

    #[Test]
    public function itReturnsNullMaxByDefault(): void
    {
        $input = DateTime::make()->name('created_at');

        $this->assertNull($input->getMax());
    }

    #[Test]
    public function itSetsMaxFluently(): void
    {
        $input = DateTime::make()->name('created_at')->max('2024-12-31T23:59:59');

        $this->assertSame('2024-12-31T23:59:59', $input->getMax());
    }

    #[Test]
    public function itEvaluatesClosureMax(): void
    {
        $input = DateTime::make()->name('created_at')->max(fn () => '2025-01-01T00:00:00');

        $this->assertSame('2025-01-01T00:00:00', $input->getMax());
    }

    #[Test]
    public function itChainsAllSetters(): void
    {
        $input = DateTime::make()
            ->name('updated_at')
            ->label('Updated At')
            ->required()
            ->min('2024-01-01T00:00:00')
            ->max('2024-12-31T23:59:59');

        $this->assertSame('updated_at', $input->getName());
        $this->assertSame('Updated At', $input->getLabel());
        $this->assertTrue($input->isRequired());
        $this->assertSame('2024-01-01T00:00:00', $input->getMin());
        $this->assertSame('2024-12-31T23:59:59', $input->getMax());
    }
}
