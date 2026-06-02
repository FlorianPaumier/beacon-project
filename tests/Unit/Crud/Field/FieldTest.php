<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Crud\Field;

use Devgeek\BeaconAdmin\Crud\Field\Field;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class FieldTest extends TestCase
{
    #[Test]
    public function itCreatesViaMake(): void
    {
        $field = $this->createField('test_name');

        $this->assertFalse($field->isRequired());
    }

    #[Test]
    public function itAutoGeneratesLabelFromName(): void
    {
        $field = $this->createField('created_at');

        $this->assertSame('Created at', $field->getLabel());
    }

    #[Test]
    public function itSetsLabelFluently(): void
    {
        $field = $this->createField('name')->label('Custom Label');

        $this->assertSame('Custom Label', $field->getLabel());
    }

    #[Test]
    public function itReturnsName(): void
    {
        $field = $this->createField('email');

        $this->assertSame('email', $field->getName());
    }

    #[Test]
    public function itDefaultsRequiredToFalse(): void
    {
        $field = $this->createField('name');

        $this->assertFalse($field->isRequired());
    }

    #[Test]
    public function itSetsRequiredFluently(): void
    {
        $field = $this->createField('name')->required();

        $this->assertTrue($field->isRequired());
    }

    #[Test]
    public function itSetsRequiredToFalse(): void
    {
        $field = $this->createField('name')->required(false);

        $this->assertFalse($field->isRequired());
    }

    #[Test]
    public function itChainsAllSetters(): void
    {
        $field = $this->createField('title')
            ->label('Post Title')
            ->required(true);

        $this->assertSame('title', $field->getName());
        $this->assertSame('Post Title', $field->getLabel());
        $this->assertTrue($field->isRequired());
    }

    private function createField(string $name): Field
    {
        $class = get_class(new class extends Field {
            public function getFormType(): string
            {
                return 'test_type';
            }
        });

        return $class::make($name);
    }
}
