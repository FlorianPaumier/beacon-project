<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Form\Component;

use Devgeek\BeaconAdmin\Form\Component\Toggle;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ToggleTest extends TestCase
{
    #[Test]
    public function itCreatesViaMake(): void
    {
        $toggle = Toggle::make()->name('test');

        $this->assertSame('test', $toggle->getName());
    }

    #[Test]
    public function itSetsNameFluently(): void
    {
        $toggle = Toggle::make()->name('active');

        $this->assertSame('active', $toggle->getName());
    }

    #[Test]
    public function itSetsLabelFluently(): void
    {
        $toggle = Toggle::make()->name('active')->label('Active');

        $this->assertSame('Active', $toggle->getLabel());
    }

    #[Test]
    public function itEvaluatesClosureLabel(): void
    {
        $toggle = Toggle::make()->name('active')->label(fn () => 'Is Active');

        $this->assertSame('Is Active', $toggle->getLabel());
    }

    #[Test]
    public function itDefaultsDefaultToFalse(): void
    {
        $toggle = Toggle::make()->name('active');

        $this->assertFalse($toggle->isDefault());
    }

    #[Test]
    public function itSetsDefaultFluently(): void
    {
        $toggle = Toggle::make()->name('active')->default();

        $this->assertTrue($toggle->isDefault());
    }

    #[Test]
    public function itEvaluatesClosureDefault(): void
    {
        $toggle = Toggle::make()->name('active')->default(fn () => true);

        $this->assertTrue($toggle->isDefault());
    }

    #[Test]
    public function itReturnsNullOnColorByDefault(): void
    {
        $toggle = Toggle::make()->name('active');

        $this->assertNull($toggle->getOnColor());
    }

    #[Test]
    public function itSetsOnColorFluently(): void
    {
        $toggle = Toggle::make()->name('active')->onColor('green');

        $this->assertSame('green', $toggle->getOnColor());
    }

    #[Test]
    public function itEvaluatesClosureOnColor(): void
    {
        $toggle = Toggle::make()->name('active')->onColor(fn () => 'blue');

        $this->assertSame('blue', $toggle->getOnColor());
    }

    #[Test]
    public function itChainsAllSetters(): void
    {
        $toggle = Toggle::make()
            ->name('notifications')
            ->label('Enable Notifications')
            ->default()
            ->onColor('blue');

        $this->assertSame('notifications', $toggle->getName());
        $this->assertSame('Enable Notifications', $toggle->getLabel());
        $this->assertTrue($toggle->isDefault());
        $this->assertSame('blue', $toggle->getOnColor());
    }
}
