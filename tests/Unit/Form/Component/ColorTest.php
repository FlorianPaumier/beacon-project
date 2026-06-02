<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Form\Component;

use Devgeek\BeaconAdmin\Form\Component\Color;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ColorTest extends TestCase
{
    #[Test]
    public function itCreatesViaMake(): void
    {
        $input = Color::make();

        $this->assertInstanceOf(Color::class, $input);
    }

    #[Test]
    public function itSetsNameFluently(): void
    {
        $input = Color::make()->name('theme_color');

        $this->assertSame('theme_color', $input->getName());
    }

    #[Test]
    public function itSetsLabelFluently(): void
    {
        $input = Color::make()->name('theme_color')->label('Theme Color');

        $this->assertSame('Theme Color', $input->getLabel());
    }

    #[Test]
    public function itEvaluatesClosureLabel(): void
    {
        $input = Color::make()->name('theme_color')->label(fn () => 'Accent Color');

        $this->assertSame('Accent Color', $input->getLabel());
    }

    #[Test]
    public function itDefaultsRequiredToFalse(): void
    {
        $input = Color::make()->name('theme_color');

        $this->assertFalse($input->isRequired());
    }

    #[Test]
    public function itSetsRequiredFluently(): void
    {
        $input = Color::make()->name('theme_color')->required();

        $this->assertTrue($input->isRequired());
    }

    #[Test]
    public function itEvaluatesClosureRequired(): void
    {
        $input = Color::make()->name('theme_color')->required(fn () => true);

        $this->assertTrue($input->isRequired());
    }

    #[Test]
    public function itChainsAllSetters(): void
    {
        $input = Color::make()
            ->name('brand_color')
            ->label('Brand Color')
            ->required();

        $this->assertSame('brand_color', $input->getName());
        $this->assertSame('Brand Color', $input->getLabel());
        $this->assertTrue($input->isRequired());
    }
}
