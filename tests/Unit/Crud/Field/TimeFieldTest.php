<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Crud\Field;

use Devgeek\BeaconAdmin\Crud\Field\TimeField;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\TimeType;

final class TimeFieldTest extends TestCase
{
    #[Test]
    public function itCreatesViaMake(): void
    {
        $field = TimeField::make('start_time');

        $this->assertInstanceOf(TimeField::class, $field);
    }

    #[Test]
    public function itAutoGeneratesLabel(): void
    {
        $field = TimeField::make('start_time');

        $this->assertSame('Start time', $field->getLabel());
    }

    #[Test]
    public function itReturnsTimeType(): void
    {
        $field = TimeField::make('start_time');

        $this->assertSame(TimeType::class, $field->getFormType());
    }

    #[Test]
    public function itInheritsRequiredFromField(): void
    {
        $field = TimeField::make('start_time')->required();

        $this->assertTrue($field->isRequired());
    }

    #[Test]
    public function itChainsInheritedSetters(): void
    {
        $field = TimeField::make('start_time')
            ->label('Start')
            ->required(true);

        $this->assertSame('start_time', $field->getName());
        $this->assertSame('Start', $field->getLabel());
        $this->assertTrue($field->isRequired());
    }
}
