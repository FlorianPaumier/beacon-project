<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Form\Component;

use Devgeek\BeaconAdmin\Form\Component\Password;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class PasswordTest extends TestCase
{
    #[Test]
    public function itCreatesViaMake(): void
    {
        $input = Password::make();

        $this->assertInstanceOf(Password::class, $input);
    }

    #[Test]
    public function itSetsNameFluently(): void
    {
        $input = Password::make()->name('password');

        $this->assertSame('password', $input->getName());
    }

    #[Test]
    public function itSetsLabelFluently(): void
    {
        $input = Password::make()->name('password')->label('Password');

        $this->assertSame('Password', $input->getLabel());
    }

    #[Test]
    public function itEvaluatesClosureLabel(): void
    {
        $input = Password::make()->name('password')->label(fn () => 'New Password');

        $this->assertSame('New Password', $input->getLabel());
    }

    #[Test]
    public function itDefaultsRequiredToFalse(): void
    {
        $input = Password::make()->name('password');

        $this->assertFalse($input->isRequired());
    }

    #[Test]
    public function itSetsRequiredFluently(): void
    {
        $input = Password::make()->name('password')->required();

        $this->assertTrue($input->isRequired());
    }

    #[Test]
    public function itEvaluatesClosureRequired(): void
    {
        $input = Password::make()->name('password')->required(fn () => true);

        $this->assertTrue($input->isRequired());
    }

    #[Test]
    public function itReturnsNullPlaceholderByDefault(): void
    {
        $input = Password::make()->name('password');

        $this->assertNull($input->getPlaceholder());
    }

    #[Test]
    public function itSetsPlaceholderFluently(): void
    {
        $input = Password::make()->name('password')->placeholder('Enter password');

        $this->assertSame('Enter password', $input->getPlaceholder());
    }

    #[Test]
    public function itEvaluatesClosurePlaceholder(): void
    {
        $input = Password::make()->name('password')->placeholder(fn () => 'Your password');

        $this->assertSame('Your password', $input->getPlaceholder());
    }

    #[Test]
    public function itReturnsNullMaxLengthByDefault(): void
    {
        $input = Password::make()->name('password');

        $this->assertNull($input->getMaxLength());
    }

    #[Test]
    public function itSetsMaxLengthFluently(): void
    {
        $input = Password::make()->name('password')->maxLength(128);

        $this->assertSame(128, $input->getMaxLength());
    }

    #[Test]
    public function itEvaluatesClosureMaxLength(): void
    {
        $input = Password::make()->name('password')->maxLength(fn () => 64);

        $this->assertSame(64, $input->getMaxLength());
    }

    #[Test]
    public function itDefaultsShowToggleToFalse(): void
    {
        $input = Password::make()->name('password');

        $this->assertFalse($input->hasShowToggle());
    }

    #[Test]
    public function itSetsShowToggleFluently(): void
    {
        $input = Password::make()->name('password')->showToggle();

        $this->assertTrue($input->hasShowToggle());
    }

    #[Test]
    public function itEvaluatesClosureShowToggle(): void
    {
        $input = Password::make()->name('password')->showToggle(fn () => true);

        $this->assertTrue($input->hasShowToggle());
    }

    #[Test]
    public function itChainsAllSetters(): void
    {
        $input = Password::make()
            ->name('user_password')
            ->label('User Password')
            ->required()
            ->placeholder('Minimum 8 characters')
            ->maxLength(255)
            ->showToggle();

        $this->assertSame('user_password', $input->getName());
        $this->assertSame('User Password', $input->getLabel());
        $this->assertTrue($input->isRequired());
        $this->assertSame('Minimum 8 characters', $input->getPlaceholder());
        $this->assertSame(255, $input->getMaxLength());
        $this->assertTrue($input->hasShowToggle());
    }
}
