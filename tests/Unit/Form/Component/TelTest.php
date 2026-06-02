<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Form\Component;

use Devgeek\BeaconAdmin\Form\Component\Tel;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class TelTest extends TestCase
{
    #[Test]
    public function itCreatesViaMake(): void
    {
        $input = Tel::make()->name('test');

        $this->assertSame('test', $input->getName());
    }

    #[Test]
    public function itSetsNameFluently(): void
    {
        $input = Tel::make()->name('phone');

        $this->assertSame('phone', $input->getName());
    }

    #[Test]
    public function itSetsLabelFluently(): void
    {
        $input = Tel::make()->name('phone')->label('Phone');

        $this->assertSame('Phone', $input->getLabel());
    }

    #[Test]
    public function itEvaluatesClosureLabel(): void
    {
        $input = Tel::make()->name('phone')->label(fn () => 'Phone Number');

        $this->assertSame('Phone Number', $input->getLabel());
    }

    #[Test]
    public function itDefaultsRequiredToFalse(): void
    {
        $input = Tel::make()->name('phone');

        $this->assertFalse($input->isRequired());
    }

    #[Test]
    public function itSetsRequiredFluently(): void
    {
        $input = Tel::make()->name('phone')->required();

        $this->assertTrue($input->isRequired());
    }

    #[Test]
    public function itEvaluatesClosureRequired(): void
    {
        $input = Tel::make()->name('phone')->required(fn () => true);

        $this->assertTrue($input->isRequired());
    }

    #[Test]
    public function itReturnsNullPlaceholderByDefault(): void
    {
        $input = Tel::make()->name('phone');

        $this->assertNull($input->getPlaceholder());
    }

    #[Test]
    public function itSetsPlaceholderFluently(): void
    {
        $input = Tel::make()->name('phone')->placeholder('+1 (555) 000-0000');

        $this->assertSame('+1 (555) 000-0000', $input->getPlaceholder());
    }

    #[Test]
    public function itEvaluatesClosurePlaceholder(): void
    {
        $input = Tel::make()->name('phone')->placeholder(fn () => 'Enter phone');

        $this->assertSame('Enter phone', $input->getPlaceholder());
    }

    #[Test]
    public function itReturnsNullPatternByDefault(): void
    {
        $input = Tel::make()->name('phone');

        $this->assertNull($input->getPattern());
    }

    #[Test]
    public function itSetsPatternFluently(): void
    {
        $input = Tel::make()->name('phone')->pattern('[0-9]{3}-[0-9]{3}-[0-9]{4}');

        $this->assertSame('[0-9]{3}-[0-9]{3}-[0-9]{4}', $input->getPattern());
    }

    #[Test]
    public function itEvaluatesClosurePattern(): void
    {
        $input = Tel::make()->name('phone')->pattern(fn () => '[0-9]+');

        $this->assertSame('[0-9]+', $input->getPattern());
    }

    #[Test]
    public function itChainsAllSetters(): void
    {
        $input = Tel::make()
            ->name('mobile')
            ->label('Mobile')
            ->required()
            ->placeholder('+1 (555) 000-0000')
            ->pattern('[0-9]{10}');

        $this->assertSame('mobile', $input->getName());
        $this->assertSame('Mobile', $input->getLabel());
        $this->assertTrue($input->isRequired());
        $this->assertSame('+1 (555) 000-0000', $input->getPlaceholder());
        $this->assertSame('[0-9]{10}', $input->getPattern());
    }
}
