<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Form\Component;

use Devgeek\BeaconAdmin\Form\Component\Checkbox;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class CheckboxTest extends TestCase
{
    #[Test]
    public function itCreatesViaMake(): void
    {
        $checkbox = Checkbox::make();

        $this->assertInstanceOf(Checkbox::class, $checkbox);
    }

    #[Test]
    public function itSetsNameFluently(): void
    {
        $checkbox = Checkbox::make()->name('agree');

        $this->assertSame('agree', $checkbox->getName());
    }

    #[Test]
    public function itSetsLabelFluently(): void
    {
        $checkbox = Checkbox::make()->name('agree')->label('Agree to terms');

        $this->assertSame('Agree to terms', $checkbox->getLabel());
    }

    #[Test]
    public function itEvaluatesClosureLabel(): void
    {
        $checkbox = Checkbox::make()->name('agree')->label(fn () => 'Dynamic label');

        $this->assertSame('Dynamic label', $checkbox->getLabel());
    }

    #[Test]
    public function itDefaultsDefaultToFalse(): void
    {
        $checkbox = Checkbox::make()->name('agree');

        $this->assertFalse($checkbox->isDefault());
    }

    #[Test]
    public function itSetsDefaultFluently(): void
    {
        $checkbox = Checkbox::make()->name('agree')->default();

        $this->assertTrue($checkbox->isDefault());
    }

    #[Test]
    public function itSetsDefaultFalse(): void
    {
        $checkbox = Checkbox::make()->name('agree')->default(false);

        $this->assertFalse($checkbox->isDefault());
    }

    #[Test]
    public function itEvaluatesClosureDefault(): void
    {
        $checkbox = Checkbox::make()->name('agree')->default(fn () => true);

        $this->assertTrue($checkbox->isDefault());
    }

    #[Test]
    public function itChainsAllSetters(): void
    {
        $checkbox = Checkbox::make()
            ->name('subscribe')
            ->label('Subscribe to newsletter')
            ->default();

        $this->assertSame('subscribe', $checkbox->getName());
        $this->assertSame('Subscribe to newsletter', $checkbox->getLabel());
        $this->assertTrue($checkbox->isDefault());
    }
}
