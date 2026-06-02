<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Crud\Field;

use Devgeek\BeaconAdmin\Crud\Field\DateField;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\DateType;

final class DateFieldTest extends TestCase
{
    #[Test]
    public function itCreatesViaMake(): void
    {
        $field = DateField::make('start_date');

        $this->assertInstanceOf(DateField::class, $field);
    }

    #[Test]
    public function itAutoGeneratesLabel(): void
    {
        $field = DateField::make('start_date');

        $this->assertSame('Start date', $field->getLabel());
    }

    #[Test]
    public function itReturnsDateType(): void
    {
        $field = DateField::make('start_date');

        $this->assertSame(DateType::class, $field->getFormType());
    }

    #[Test]
    public function itInheritsRequiredFromField(): void
    {
        $field = DateField::make('start_date')->required();

        $this->assertTrue($field->isRequired());
    }

    #[Test]
    public function itChainsInheritedSetters(): void
    {
        $field = DateField::make('start_date')
            ->label('Start')
            ->required(true);

        $this->assertSame('start_date', $field->getName());
        $this->assertSame('Start', $field->getLabel());
        $this->assertTrue($field->isRequired());
    }
}
