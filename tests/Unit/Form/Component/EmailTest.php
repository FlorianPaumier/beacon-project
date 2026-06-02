<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Form\Component;

use Devgeek\BeaconAdmin\Form\Component\Email;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class EmailTest extends TestCase
{
    #[Test]
    public function itCreatesViaMake(): void
    {
        $input = Email::make();

        $this->assertNull($input->getLabel());
    }

    #[Test]
    public function itSetsNameFluently(): void
    {
        $input = Email::make()->name('email');

        $this->assertSame('email', $input->getName());
    }

    #[Test]
    public function itSetsLabelFluently(): void
    {
        $input = Email::make()->name('email')->label('Email Address');

        $this->assertSame('Email Address', $input->getLabel());
    }

    #[Test]
    public function itEvaluatesClosureLabel(): void
    {
        $input = Email::make()->name('email')->label(fn () => 'Work Email');

        $this->assertSame('Work Email', $input->getLabel());
    }

    #[Test]
    public function itDefaultsRequiredToFalse(): void
    {
        $input = Email::make()->name('email');

        $this->assertFalse($input->isRequired());
    }

    #[Test]
    public function itSetsRequiredFluently(): void
    {
        $input = Email::make()->name('email')->required();

        $this->assertTrue($input->isRequired());
    }

    #[Test]
    public function itEvaluatesClosureRequired(): void
    {
        $input = Email::make()->name('email')->required(fn () => true);

        $this->assertTrue($input->isRequired());
    }

    #[Test]
    public function itReturnsNullPlaceholderByDefault(): void
    {
        $input = Email::make()->name('email');

        $this->assertNull($input->getPlaceholder());
    }

    #[Test]
    public function itSetsPlaceholderFluently(): void
    {
        $input = Email::make()->name('email')->placeholder('you@example.com');

        $this->assertSame('you@example.com', $input->getPlaceholder());
    }

    #[Test]
    public function itEvaluatesClosurePlaceholder(): void
    {
        $input = Email::make()->name('email')->placeholder(fn () => 'Enter email');

        $this->assertSame('Enter email', $input->getPlaceholder());
    }

    #[Test]
    public function itDefaultsMultipleToFalse(): void
    {
        $input = Email::make()->name('email');

        $this->assertFalse($input->isMultiple());
    }

    #[Test]
    public function itSetsMultipleFluently(): void
    {
        $input = Email::make()->name('email')->multiple();

        $this->assertTrue($input->isMultiple());
    }

    #[Test]
    public function itEvaluatesClosureMultiple(): void
    {
        $input = Email::make()->name('email')->multiple(fn () => true);

        $this->assertTrue($input->isMultiple());
    }

    #[Test]
    public function itChainsAllSetters(): void
    {
        $input = Email::make()
            ->name('contact_email')
            ->label('Contact Email')
            ->required()
            ->placeholder('contact@example.com')
            ->multiple();

        $this->assertSame('contact_email', $input->getName());
        $this->assertSame('Contact Email', $input->getLabel());
        $this->assertTrue($input->isRequired());
        $this->assertSame('contact@example.com', $input->getPlaceholder());
        $this->assertTrue($input->isMultiple());
    }
}
