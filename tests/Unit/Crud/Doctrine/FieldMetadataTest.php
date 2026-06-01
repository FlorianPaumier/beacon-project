<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Crud\Doctrine;

use Devgeek\BeaconAdmin\Crud\Doctrine\FieldMetadata;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class FieldMetadataTest extends TestCase
{
    #[Test]
    public function itCreatesViaMake(): void
    {
        $field = FieldMetadata::make();

        $this->assertInstanceOf(FieldMetadata::class, $field);
    }

    #[Test]
    public function itSetsName(): void
    {
        $field = FieldMetadata::make()->name('email');

        $this->assertSame('email', $field->getName());
    }

    #[Test]
    public function itSetsType(): void
    {
        $field = FieldMetadata::make()->type('string');

        $this->assertSame('string', $field->getType());
    }

    #[Test]
    public function itDefaultsNullableToFalse(): void
    {
        $field = FieldMetadata::make();

        $this->assertFalse($field->isNullable());
    }

    #[Test]
    public function itSetsNullable(): void
    {
        $field = FieldMetadata::make()->nullable(true);

        $this->assertTrue($field->isNullable());
    }

    #[Test]
    public function itDefaultsLengthToNull(): void
    {
        $field = FieldMetadata::make();

        $this->assertNull($field->getLength());
    }

    #[Test]
    public function itSetsLength(): void
    {
        $field = FieldMetadata::make()->length(255);

        $this->assertSame(255, $field->getLength());
    }

    #[Test]
    public function itDefaultsUniqueToFalse(): void
    {
        $field = FieldMetadata::make();

        $this->assertFalse($field->isUnique());
    }

    #[Test]
    public function itSetsUnique(): void
    {
        $field = FieldMetadata::make()->unique(true);

        $this->assertTrue($field->isUnique());
    }

    #[Test]
    public function itChainsAllSetters(): void
    {
        $field = FieldMetadata::make()
            ->name('title')
            ->type('string')
            ->nullable(false)
            ->length(100)
            ->unique(true);

        $this->assertSame('title', $field->getName());
        $this->assertSame('string', $field->getType());
        $this->assertFalse($field->isNullable());
        $this->assertSame(100, $field->getLength());
        $this->assertTrue($field->isUnique());
    }
}
