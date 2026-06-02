<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Crud\Field;

use Devgeek\BeaconAdmin\Crud\Field\BooleanField;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

final class BooleanFieldTest extends TestCase
{
    #[Test]
    public function itCreatesViaMake(): void
    {
        $field = BooleanField::make('is_active');

        $this->assertSame('is_active', $field->getName());
    }

    #[Test]
    public function itAutoGeneratesLabel(): void
    {
        $field = BooleanField::make('is_active');

        $this->assertSame('Is active', $field->getLabel());
    }

    #[Test]
    public function itReturnsCheckboxType(): void
    {
        $field = BooleanField::make('is_active');

        $this->assertSame(CheckboxType::class, $field->getFormType());
    }

    #[Test]
    public function itInheritsRequiredFromField(): void
    {
        $field = BooleanField::make('is_active')->required(true);

        $this->assertTrue($field->isRequired());
    }

    #[Test]
    public function itChainsInheritedSetters(): void
    {
        $field = BooleanField::make('published')
            ->label('Published')
            ->required(true);

        $this->assertSame('published', $field->getName());
        $this->assertSame('Published', $field->getLabel());
        $this->assertTrue($field->isRequired());
    }
}
