<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Form\Component;

use Devgeek\BeaconAdmin\Form\Component\Fieldset;
use Devgeek\BeaconAdmin\Support\Component;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class FieldsetTest extends TestCase
{
    #[Test]
    public function itCreatesViaMake(): void
    {
        $fieldset = Fieldset::make();

        $this->assertNull($fieldset->getLabel());
    }

    #[Test]
    public function itSetsLabelFluently(): void
    {
        $fieldset = Fieldset::make()->label('Contact Information');

        $this->assertSame('Contact Information', $fieldset->getLabel());
    }

    #[Test]
    public function itReturnsNullLabelByDefault(): void
    {
        $fieldset = Fieldset::make();

        $this->assertNull($fieldset->getLabel());
    }

    #[Test]
    public function itEvaluatesClosureLabel(): void
    {
        $fieldset = Fieldset::make()->label(fn () => 'Dynamic Label');

        $this->assertSame('Dynamic Label', $fieldset->getLabel());
    }

    #[Test]
    public function itSetsSchemaFluently(): void
    {
        $component = $this->createMock(Component::class);
        $fieldset = Fieldset::make()->schema([$component]);

        $this->assertCount(1, $fieldset->getSchema());
        $this->assertSame($component, $fieldset->getSchema()[0]);
    }

    #[Test]
    public function itReturnsEmptySchemaByDefault(): void
    {
        $fieldset = Fieldset::make();

        $this->assertSame([], $fieldset->getSchema());
    }

    #[Test]
    public function itChainsAllSetters(): void
    {
        $component = $this->createMock(Component::class);
        $fieldset = Fieldset::make()
            ->label('Details')
            ->schema([$component]);

        $this->assertSame('Details', $fieldset->getLabel());
        $this->assertCount(1, $fieldset->getSchema());
    }
}
