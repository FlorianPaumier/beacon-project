<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Crud\Field;

use Devgeek\BeaconAdmin\Crud\Field\BooleanField;
use Devgeek\BeaconAdmin\Crud\Field\DateField;
use Devgeek\BeaconAdmin\Crud\Field\DateTimeField;
use Devgeek\BeaconAdmin\Crud\Field\EmailField;
use Devgeek\BeaconAdmin\Crud\Field\FieldRegistry;
use Devgeek\BeaconAdmin\Crud\Field\NumberField;
use Devgeek\BeaconAdmin\Crud\Field\TextField;
use Devgeek\BeaconAdmin\Crud\Field\TimeField;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class FieldRegistryTest extends TestCase
{
    #[Test]
    public function itReturnsTextFieldForString(): void
    {
        $registry = new FieldRegistry();

        $this->assertSame(TextField::class, $registry->getField('string'));
    }

    #[Test]
    public function itReturnsTextFieldForText(): void
    {
        $registry = new FieldRegistry();

        $this->assertSame(TextField::class, $registry->getField('text'));
    }

    #[Test]
    public function itReturnsNumberFieldForInteger(): void
    {
        $registry = new FieldRegistry();

        $this->assertSame(NumberField::class, $registry->getField('integer'));
    }

    #[Test]
    public function itReturnsNumberFieldForFloat(): void
    {
        $registry = new FieldRegistry();

        $this->assertSame(NumberField::class, $registry->getField('float'));
    }

    #[Test]
    public function itReturnsNumberFieldForDecimal(): void
    {
        $registry = new FieldRegistry();

        $this->assertSame(NumberField::class, $registry->getField('decimal'));
    }

    #[Test]
    public function itReturnsBooleanFieldForBoolean(): void
    {
        $registry = new FieldRegistry();

        $this->assertSame(BooleanField::class, $registry->getField('boolean'));
    }

    #[Test]
    public function itReturnsDateTimeFieldForDatetime(): void
    {
        $registry = new FieldRegistry();

        $this->assertSame(DateTimeField::class, $registry->getField('datetime'));
    }

    #[Test]
    public function itReturnsDateTimeFieldForDatetimetz(): void
    {
        $registry = new FieldRegistry();

        $this->assertSame(DateTimeField::class, $registry->getField('datetimetz'));
    }

    #[Test]
    public function itReturnsDateFieldForDate(): void
    {
        $registry = new FieldRegistry();

        $this->assertSame(DateField::class, $registry->getField('date'));
    }

    #[Test]
    public function itReturnsTimeFieldForTime(): void
    {
        $registry = new FieldRegistry();

        $this->assertSame(TimeField::class, $registry->getField('time'));
    }

    #[Test]
    public function itReturnsEmailFieldForEmail(): void
    {
        $registry = new FieldRegistry();

        $this->assertSame(EmailField::class, $registry->getField('email'));
    }

    #[Test]
    public function itReturnsNullForUnknownType(): void
    {
        $registry = new FieldRegistry();

        $this->assertNull($registry->getField('unknown_type'));
    }

    #[Test]
    public function itRegistersNewField(): void
    {
        $registry = new FieldRegistry();
        $registry->register('custom_type', TextField::class);

        $this->assertSame(TextField::class, $registry->getField('custom_type'));
    }

    #[Test]
    public function itOverridesExistingMapping(): void
    {
        $registry = new FieldRegistry();
        $registry->register('string', NumberField::class);

        $this->assertSame(NumberField::class, $registry->getField('string'));
    }

    #[Test]
    public function itReturnsAllFields(): void
    {
        $registry = new FieldRegistry();

        $fields = $registry->getFields();
        $this->assertArrayHasKey('string', $fields);
        $this->assertArrayHasKey('email', $fields);
        $this->assertCount(11, $fields);
    }

    #[Test]
    public function itIncludesRegisteredFieldInGetFields(): void
    {
        $registry = new FieldRegistry();
        $registry->register('json', TextField::class);

        $fields = $registry->getFields();
        $this->assertArrayHasKey('json', $fields);
        $this->assertSame(TextField::class, $fields['json']);
    }
}
