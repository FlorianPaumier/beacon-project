<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Form\Component;

use Devgeek\BeaconAdmin\Form\Component\Radio;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class RadioTest extends TestCase
{
    #[Test]
    public function itCreatesViaMake(): void
    {
        $input = Radio::make()->name('test');

        $this->assertSame('test', $input->getName());
    }

    #[Test]
    public function itSetsNameFluently(): void
    {
        $input = Radio::make()->name('gender');

        $this->assertSame('gender', $input->getName());
    }

    #[Test]
    public function itSetsLabelFluently(): void
    {
        $input = Radio::make()->name('gender')->label('Gender');

        $this->assertSame('Gender', $input->getLabel());
    }

    #[Test]
    public function itEvaluatesClosureLabel(): void
    {
        $input = Radio::make()->name('gender')->label(fn () => 'Select Gender');

        $this->assertSame('Select Gender', $input->getLabel());
    }

    #[Test]
    public function itDefaultsRequiredToFalse(): void
    {
        $input = Radio::make()->name('gender');

        $this->assertFalse($input->isRequired());
    }

    #[Test]
    public function itSetsRequiredFluently(): void
    {
        $input = Radio::make()->name('gender')->required();

        $this->assertTrue($input->isRequired());
    }

    #[Test]
    public function itEvaluatesClosureRequired(): void
    {
        $input = Radio::make()->name('gender')->required(fn () => true);

        $this->assertTrue($input->isRequired());
    }

    #[Test]
    public function itReturnsEmptyOptionsByDefault(): void
    {
        $input = Radio::make()->name('gender');

        $this->assertSame([], $input->getOptions());
    }

    #[Test]
    public function itSetsOptionsFluently(): void
    {
        $options = ['male' => 'Male', 'female' => 'Female'];
        $input = Radio::make()->name('gender')->options($options);

        $this->assertSame($options, $input->getOptions());
    }

    #[Test]
    public function itEvaluatesClosureOptions(): void
    {
        $input = Radio::make()->name('gender')->options(fn () => ['yes' => 'Yes']);

        $this->assertSame(['yes' => 'Yes'], $input->getOptions());
    }

    #[Test]
    public function itDefaultsLayoutToVertical(): void
    {
        $input = Radio::make()->name('gender');

        $this->assertSame('vertical', $input->getLayout());
    }

    #[Test]
    public function itSetsLayoutFluently(): void
    {
        $input = Radio::make()->name('gender')->layout('horizontal');

        $this->assertSame('horizontal', $input->getLayout());
    }

    #[Test]
    public function itEvaluatesClosureLayout(): void
    {
        $input = Radio::make()->name('gender')->layout(fn () => 'horizontal');

        $this->assertSame('horizontal', $input->getLayout());
    }

    #[Test]
    public function itChainsAllSetters(): void
    {
        $options = ['option_a' => 'Option A', 'option_b' => 'Option B'];
        $input = Radio::make()
            ->name('choice')
            ->label('Choice')
            ->required()
            ->options($options)
            ->layout('horizontal');

        $this->assertSame('choice', $input->getName());
        $this->assertSame('Choice', $input->getLabel());
        $this->assertTrue($input->isRequired());
        $this->assertSame($options, $input->getOptions());
        $this->assertSame('horizontal', $input->getLayout());
    }
}
