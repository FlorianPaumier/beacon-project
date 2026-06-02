<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Crud\Field;

use Devgeek\BeaconAdmin\Crud\Field\DateTimeField;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

final class DateTimeFieldTest extends TestCase
{
    #[Test]
    public function itCreatesViaMake(): void
    {
        $field = DateTimeField::make('created_at');

        $this->assertSame('created_at', $field->getName());
    }

    #[Test]
    public function itAutoGeneratesLabel(): void
    {
        $field = DateTimeField::make('created_at');

        $this->assertSame('Created at', $field->getLabel());
    }

    #[Test]
    public function itDefaultsFormat(): void
    {
        $field = DateTimeField::make('created_at');

        $this->assertSame('Y-m-d H:i', $field->getFormat());
    }

    #[Test]
    public function itSetsFormat(): void
    {
        $field = DateTimeField::make('created_at')->format('d/m/Y');

        $this->assertSame('d/m/Y', $field->getFormat());
    }

    #[Test]
    public function itReturnsDateTimeType(): void
    {
        $field = DateTimeField::make('created_at');

        $this->assertSame(DateTimeType::class, $field->getFormType());
    }

    #[Test]
    public function itChainsAllSetters(): void
    {
        $field = DateTimeField::make('updated_at')
            ->label('Updated At')
            ->required(true)
            ->format('Y/m/d');

        $this->assertSame('updated_at', $field->getName());
        $this->assertSame('Updated At', $field->getLabel());
        $this->assertTrue($field->isRequired());
        $this->assertSame('Y/m/d', $field->getFormat());
    }
}
