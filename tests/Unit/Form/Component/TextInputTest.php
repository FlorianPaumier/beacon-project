<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Form\Component;

use Devgeek\BeaconAdmin\Form\Component\TextInput;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class TextInputTest extends TestCase
{
    #[Test]
    public function itCreatesViaMake(): void
    {
        $input = TextInput::make();

        $this->assertInstanceOf(TextInput::class, $input);
    }

    #[Test]
    public function itSetsNameFluently(): void
    {
        $input = TextInput::make()->name('email');

        $this->assertSame('email', $input->getName());
    }

    #[Test]
    public function itSetsLabelFluently(): void
    {
        $input = TextInput::make()->name('email')->label('Email Address');

        $this->assertSame('Email Address', $input->getLabel());
    }

    #[Test]
    public function itReturnsNullLabelByDefault(): void
    {
        $input = TextInput::make()->name('email');

        $this->assertNull($input->getLabel());
    }

    #[Test]
    public function itEvaluatesClosureLabel(): void
    {
        $input = TextInput::make()->name('email')->label(fn () => 'Dynamic Label');

        $this->assertSame('Dynamic Label', $input->getLabel());
    }

    #[Test]
    public function itDefaultsRequiredToFalse(): void
    {
        $input = TextInput::make()->name('email');

        $this->assertFalse($input->isRequired());
    }

    #[Test]
    public function itSetsRequiredFluently(): void
    {
        $input = TextInput::make()->name('email')->required();

        $this->assertTrue($input->isRequired());
    }

    #[Test]
    public function itSetsRequiredFalse(): void
    {
        $input = TextInput::make()->name('email')->required(false);

        $this->assertFalse($input->isRequired());
    }

    #[Test]
    public function itEvaluatesClosureRequired(): void
    {
        $input = TextInput::make()->name('email')->required(fn () => true);

        $this->assertTrue($input->isRequired());
    }

    #[Test]
    public function itDefaultsEmailToFalse(): void
    {
        $input = TextInput::make()->name('email');

        $this->assertFalse($input->isEmail());
    }

    #[Test]
    public function itSetsEmailFluently(): void
    {
        $input = TextInput::make()->name('email')->email();

        $this->assertTrue($input->isEmail());
    }

    #[Test]
    public function itEvaluatesClosureEmail(): void
    {
        $input = TextInput::make()->name('email')->email(fn () => true);

        $this->assertTrue($input->isEmail());
    }

    #[Test]
    public function itReturnsNullMaxLengthByDefault(): void
    {
        $input = TextInput::make()->name('title');

        $this->assertNull($input->getMaxLength());
    }

    #[Test]
    public function itSetsMaxLengthFluently(): void
    {
        $input = TextInput::make()->name('title')->maxLength(255);

        $this->assertSame(255, $input->getMaxLength());
    }

    #[Test]
    public function itEvaluatesClosureMaxLength(): void
    {
        $input = TextInput::make()->name('title')->maxLength(fn () => 100);

        $this->assertSame(100, $input->getMaxLength());
    }

    #[Test]
    public function itReturnsNullPlaceholderByDefault(): void
    {
        $input = TextInput::make()->name('email');

        $this->assertNull($input->getPlaceholder());
    }

    #[Test]
    public function itSetsPlaceholderFluently(): void
    {
        $input = TextInput::make()->name('email')->placeholder('Enter your email');

        $this->assertSame('Enter your email', $input->getPlaceholder());
    }

    #[Test]
    public function itEvaluatesClosurePlaceholder(): void
    {
        $input = TextInput::make()->name('email')->placeholder(fn () => 'Dynamic placeholder');

        $this->assertSame('Dynamic placeholder', $input->getPlaceholder());
    }

    #[Test]
    public function itChainsAllSetters(): void
    {
        $input = TextInput::make()
            ->name('username')
            ->label('Username')
            ->required()
            ->maxLength(50)
            ->placeholder('Enter username');

        $this->assertSame('username', $input->getName());
        $this->assertSame('Username', $input->getLabel());
        $this->assertTrue($input->isRequired());
        $this->assertSame(50, $input->getMaxLength());
        $this->assertSame('Enter username', $input->getPlaceholder());
    }
}
