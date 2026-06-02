<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Form\Component;

use Devgeek\BeaconAdmin\Form\Component\Repeater;
use Devgeek\BeaconAdmin\Form\Component\TextInput;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class RepeaterTest extends TestCase
{
    #[Test]
    public function itCreatesViaMake(): void
    {
        $input = Repeater::make()->name('test');

        $this->assertSame('test', $input->getName());
    }

    #[Test]
    public function itSetsNameFluently(): void
    {
        $input = Repeater::make()->name('line_items');

        $this->assertSame('line_items', $input->getName());
    }

    #[Test]
    public function itSetsLabelFluently(): void
    {
        $input = Repeater::make()->name('items')->label('Items');

        $this->assertSame('Items', $input->getLabel());
    }

    #[Test]
    public function itEvaluatesClosureLabel(): void
    {
        $input = Repeater::make()->name('items')->label(fn () => 'Line Items');

        $this->assertSame('Line Items', $input->getLabel());
    }

    #[Test]
    public function itReturnsEmptySchemaByDefault(): void
    {
        $input = Repeater::make()->name('items');

        $this->assertSame([], $input->getSchema());
    }

    #[Test]
    public function itSetsSchemaFluently(): void
    {
        $schema = [TextInput::make()->name('name')];
        $input = Repeater::make()->name('items')->schema($schema);

        $this->assertSame($schema, $input->getSchema());
    }

    #[Test]
    public function itReturnsNullMinItemsByDefault(): void
    {
        $input = Repeater::make()->name('items');

        $this->assertNull($input->getMinItems());
    }

    #[Test]
    public function itSetsMinItemsFluently(): void
    {
        $input = Repeater::make()->name('items')->minItems(1);

        $this->assertSame(1, $input->getMinItems());
    }

    #[Test]
    public function itEvaluatesClosureMinItems(): void
    {
        $input = Repeater::make()->name('items')->minItems(fn () => 2);

        $this->assertSame(2, $input->getMinItems());
    }

    #[Test]
    public function itReturnsNullMaxItemsByDefault(): void
    {
        $input = Repeater::make()->name('items');

        $this->assertNull($input->getMaxItems());
    }

    #[Test]
    public function itSetsMaxItemsFluently(): void
    {
        $input = Repeater::make()->name('items')->maxItems(10);

        $this->assertSame(10, $input->getMaxItems());
    }

    #[Test]
    public function itEvaluatesClosureMaxItems(): void
    {
        $input = Repeater::make()->name('items')->maxItems(fn () => 5);

        $this->assertSame(5, $input->getMaxItems());
    }

    #[Test]
    public function itDefaultsAddLabelToAdd(): void
    {
        $input = Repeater::make()->name('items');

        $this->assertSame('Add', $input->getAddLabel());
    }

    #[Test]
    public function itSetsAddLabelFluently(): void
    {
        $input = Repeater::make()->name('items')->addLabel('Add Item');

        $this->assertSame('Add Item', $input->getAddLabel());
    }

    #[Test]
    public function itEvaluatesClosureAddLabel(): void
    {
        $input = Repeater::make()->name('items')->addLabel(fn () => 'Add Row');

        $this->assertSame('Add Row', $input->getAddLabel());
    }

    #[Test]
    public function itChainsAllSetters(): void
    {
        $schema = [TextInput::make()->name('name')];
        $input = Repeater::make()
            ->name('rows')
            ->label('Rows')
            ->schema($schema)
            ->minItems(1)
            ->maxItems(20)
            ->addLabel('Add Row');

        $this->assertSame('rows', $input->getName());
        $this->assertSame('Rows', $input->getLabel());
        $this->assertSame($schema, $input->getSchema());
        $this->assertSame(1, $input->getMinItems());
        $this->assertSame(20, $input->getMaxItems());
        $this->assertSame('Add Row', $input->getAddLabel());
    }
}
