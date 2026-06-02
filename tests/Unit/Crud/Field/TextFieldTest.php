<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Crud\Field;

use Devgeek\BeaconAdmin\Crud\Field\TextField;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class TextFieldTest extends TestCase
{
    #[Test]
    public function itCreatesViaMake(): void
    {
        $field = TextField::make('name');

        $this->assertSame('name', $field->getName());
    }

    #[Test]
    public function itAutoGeneratesLabel(): void
    {
        $field = TextField::make('user_name');

        $this->assertSame('User name', $field->getLabel());
    }

    #[Test]
    public function itDefaultsMaxLengthToNull(): void
    {
        $field = TextField::make('name');

        $this->assertNull($field->getMaxLength());
    }

    #[Test]
    public function itSetsMaxLength(): void
    {
        $field = TextField::make('name')->maxLength(255);

        $this->assertSame(255, $field->getMaxLength());
    }

    #[Test]
    public function itResetsMaxLengthToNull(): void
    {
        $field = TextField::make('name')->maxLength(255)->maxLength(null);

        $this->assertNull($field->getMaxLength());
    }

    #[Test]
    public function itDefaultsPlaceholderToNull(): void
    {
        $field = TextField::make('name');

        $this->assertNull($field->getPlaceholder());
    }

    #[Test]
    public function itSetsPlaceholder(): void
    {
        $field = TextField::make('name')->placeholder('Enter name');

        $this->assertSame('Enter name', $field->getPlaceholder());
    }

    #[Test]
    public function itResetsPlaceholderToNull(): void
    {
        $field = TextField::make('name')->placeholder('Enter name')->placeholder(null);

        $this->assertNull($field->getPlaceholder());
    }

    #[Test]
    public function itReturnsTextType(): void
    {
        $field = TextField::make('name');

        $this->assertSame(TextType::class, $field->getFormType());
    }

    #[Test]
    public function itChainsAllSetters(): void
    {
        $field = TextField::make('description')
            ->label('Description')
            ->required(true)
            ->maxLength(500)
            ->placeholder('Describe...');

        $this->assertSame('description', $field->getName());
        $this->assertSame('Description', $field->getLabel());
        $this->assertTrue($field->isRequired());
        $this->assertSame(500, $field->getMaxLength());
        $this->assertSame('Describe...', $field->getPlaceholder());
    }
}
