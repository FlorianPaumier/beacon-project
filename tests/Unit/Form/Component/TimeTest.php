<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Form\Component;

use Devgeek\BeaconAdmin\Form\Component\Time;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class TimeTest extends TestCase
{
    #[Test]
    public function itCreatesViaMake(): void
    {
        $input = Time::make()->name('test');

        $this->assertSame('test', $input->getName());
    }

    #[Test]
    public function itSetsNameFluently(): void
    {
        $input = Time::make()->name('start_time');

        $this->assertSame('start_time', $input->getName());
    }

    #[Test]
    public function itSetsLabelFluently(): void
    {
        $input = Time::make()->name('start_time')->label('Start Time');

        $this->assertSame('Start Time', $input->getLabel());
    }

    #[Test]
    public function itEvaluatesClosureLabel(): void
    {
        $input = Time::make()->name('start_time')->label(fn () => 'Opening Time');

        $this->assertSame('Opening Time', $input->getLabel());
    }

    #[Test]
    public function itDefaultsRequiredToFalse(): void
    {
        $input = Time::make()->name('start_time');

        $this->assertFalse($input->isRequired());
    }

    #[Test]
    public function itSetsRequiredFluently(): void
    {
        $input = Time::make()->name('start_time')->required();

        $this->assertTrue($input->isRequired());
    }

    #[Test]
    public function itEvaluatesClosureRequired(): void
    {
        $input = Time::make()->name('start_time')->required(fn () => true);

        $this->assertTrue($input->isRequired());
    }

    #[Test]
    public function itChainsAllSetters(): void
    {
        $input = Time::make()
            ->name('end_time')
            ->label('End Time')
            ->required();

        $this->assertSame('end_time', $input->getName());
        $this->assertSame('End Time', $input->getLabel());
        $this->assertTrue($input->isRequired());
    }
}
