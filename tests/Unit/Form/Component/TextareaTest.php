<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Form\Component;

use Devgeek\BeaconAdmin\Form\Component\Textarea;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class TextareaTest extends TestCase
{
    #[Test]
    public function itCreatesViaMake(): void
    {
        $input = Textarea::make();

        $this->assertInstanceOf(Textarea::class, $input);
    }

    #[Test]
    public function itSetsNameFluently(): void
    {
        $input = Textarea::make()->name('bio');

        $this->assertSame('bio', $input->getName());
    }

    #[Test]
    public function itSetsLabelFluently(): void
    {
        $input = Textarea::make()->name('bio')->label('Biography');

        $this->assertSame('Biography', $input->getLabel());
    }

    #[Test]
    public function itEvaluatesClosureLabel(): void
    {
        $input = Textarea::make()->name('bio')->label(fn () => 'Dynamic Label');

        $this->assertSame('Dynamic Label', $input->getLabel());
    }

    #[Test]
    public function itDefaultsRequiredToFalse(): void
    {
        $input = Textarea::make()->name('bio');

        $this->assertFalse($input->isRequired());
    }

    #[Test]
    public function itSetsRequiredFluently(): void
    {
        $input = Textarea::make()->name('bio')->required();

        $this->assertTrue($input->isRequired());
    }

    #[Test]
    public function itEvaluatesClosureRequired(): void
    {
        $input = Textarea::make()->name('bio')->required(fn () => true);

        $this->assertTrue($input->isRequired());
    }

    #[Test]
    public function itReturnsNullPlaceholderByDefault(): void
    {
        $input = Textarea::make()->name('bio');

        $this->assertNull($input->getPlaceholder());
    }

    #[Test]
    public function itSetsPlaceholderFluently(): void
    {
        $input = Textarea::make()->name('bio')->placeholder('Write something');

        $this->assertSame('Write something', $input->getPlaceholder());
    }

    #[Test]
    public function itEvaluatesClosurePlaceholder(): void
    {
        $input = Textarea::make()->name('bio')->placeholder(fn () => 'Dynamic');

        $this->assertSame('Dynamic', $input->getPlaceholder());
    }

    #[Test]
    public function itReturnsNullMaxLengthByDefault(): void
    {
        $input = Textarea::make()->name('bio');

        $this->assertNull($input->getMaxLength());
    }

    #[Test]
    public function itSetsMaxLengthFluently(): void
    {
        $input = Textarea::make()->name('bio')->maxLength(500);

        $this->assertSame(500, $input->getMaxLength());
    }

    #[Test]
    public function itEvaluatesClosureMaxLength(): void
    {
        $input = Textarea::make()->name('bio')->maxLength(fn () => 200);

        $this->assertSame(200, $input->getMaxLength());
    }

    #[Test]
    public function itDefaultsRowsToThree(): void
    {
        $input = Textarea::make()->name('bio');

        $this->assertSame(3, $input->getRows());
    }

    #[Test]
    public function itSetsRowsFluently(): void
    {
        $input = Textarea::make()->name('bio')->rows(5);

        $this->assertSame(5, $input->getRows());
    }

    #[Test]
    public function itEvaluatesClosureRows(): void
    {
        $input = Textarea::make()->name('bio')->rows(fn () => 10);

        $this->assertSame(10, $input->getRows());
    }

    #[Test]
    public function itDefaultsAutoResizeToFalse(): void
    {
        $input = Textarea::make()->name('bio');

        $this->assertFalse($input->isAutoResize());
    }

    #[Test]
    public function itSetsAutoResizeFluently(): void
    {
        $input = Textarea::make()->name('bio')->autoResize();

        $this->assertTrue($input->isAutoResize());
    }

    #[Test]
    public function itEvaluatesClosureAutoResize(): void
    {
        $input = Textarea::make()->name('bio')->autoResize(fn () => true);

        $this->assertTrue($input->isAutoResize());
    }

    #[Test]
    public function itChainsAllSetters(): void
    {
        $input = Textarea::make()
            ->name('description')
            ->label('Description')
            ->required()
            ->placeholder('Enter description')
            ->maxLength(1000)
            ->rows(6)
            ->autoResize();

        $this->assertSame('description', $input->getName());
        $this->assertSame('Description', $input->getLabel());
        $this->assertTrue($input->isRequired());
        $this->assertSame('Enter description', $input->getPlaceholder());
        $this->assertSame(1000, $input->getMaxLength());
        $this->assertSame(6, $input->getRows());
        $this->assertTrue($input->isAutoResize());
    }
}
