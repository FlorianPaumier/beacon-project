<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Form\Component;

use Devgeek\BeaconAdmin\Form\Component\KeyValue;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class KeyValueTest extends TestCase
{
    #[Test]
    public function itCreatesViaMake(): void
    {
        $input = KeyValue::make()->name('test');

        $this->assertSame('test', $input->getName());
    }

    #[Test]
    public function itSetsNameFluently(): void
    {
        $input = KeyValue::make()->name('meta');

        $this->assertSame('meta', $input->getName());
    }

    #[Test]
    public function itSetsLabelFluently(): void
    {
        $input = KeyValue::make()->name('meta')->label('Metadata');

        $this->assertSame('Metadata', $input->getLabel());
    }

    #[Test]
    public function itEvaluatesClosureLabel(): void
    {
        $input = KeyValue::make()->name('meta')->label(fn () => 'Dynamic');

        $this->assertSame('Dynamic', $input->getLabel());
    }

    #[Test]
    public function itDefaultsRequiredToFalse(): void
    {
        $input = KeyValue::make()->name('meta');

        $this->assertFalse($input->isRequired());
    }

    #[Test]
    public function itSetsRequiredFluently(): void
    {
        $input = KeyValue::make()->name('meta')->required();

        $this->assertTrue($input->isRequired());
    }

    #[Test]
    public function itEvaluatesClosureRequired(): void
    {
        $input = KeyValue::make()->name('meta')->required(fn () => true);

        $this->assertTrue($input->isRequired());
    }

    #[Test]
    public function itReturnsNullKeyPlaceholderByDefault(): void
    {
        $input = KeyValue::make()->name('meta');

        $this->assertNull($input->getKeyPlaceholder());
    }

    #[Test]
    public function itSetsKeyPlaceholderFluently(): void
    {
        $input = KeyValue::make()->name('meta')->keyPlaceholder('Setting');

        $this->assertSame('Setting', $input->getKeyPlaceholder());
    }

    #[Test]
    public function itEvaluatesClosureKeyPlaceholder(): void
    {
        $input = KeyValue::make()->name('meta')->keyPlaceholder(fn () => 'Key');

        $this->assertSame('Key', $input->getKeyPlaceholder());
    }

    #[Test]
    public function itReturnsNullValuePlaceholderByDefault(): void
    {
        $input = KeyValue::make()->name('meta');

        $this->assertNull($input->getValuePlaceholder());
    }

    #[Test]
    public function itSetsValuePlaceholderFluently(): void
    {
        $input = KeyValue::make()->name('meta')->valuePlaceholder('Value');

        $this->assertSame('Value', $input->getValuePlaceholder());
    }

    #[Test]
    public function itEvaluatesClosureValuePlaceholder(): void
    {
        $input = KeyValue::make()->name('meta')->valuePlaceholder(fn () => 'Val');

        $this->assertSame('Val', $input->getValuePlaceholder());
    }

    #[Test]
    public function itDefaultsAllowDeleteToTrue(): void
    {
        $input = KeyValue::make()->name('meta');

        $this->assertTrue($input->isAllowDelete());
    }

    #[Test]
    public function itSetsAllowDeleteFluently(): void
    {
        $input = KeyValue::make()->name('meta')->allowDelete(false);

        $this->assertFalse($input->isAllowDelete());
    }

    #[Test]
    public function itEvaluatesClosureAllowDelete(): void
    {
        $input = KeyValue::make()->name('meta')->allowDelete(fn () => false);

        $this->assertFalse($input->isAllowDelete());
    }

    #[Test]
    public function itDefaultsAllowAddToTrue(): void
    {
        $input = KeyValue::make()->name('meta');

        $this->assertTrue($input->isAllowAdd());
    }

    #[Test]
    public function itSetsAllowAddFluently(): void
    {
        $input = KeyValue::make()->name('meta')->allowAdd(false);

        $this->assertFalse($input->isAllowAdd());
    }

    #[Test]
    public function itEvaluatesClosureAllowAdd(): void
    {
        $input = KeyValue::make()->name('meta')->allowAdd(fn () => false);

        $this->assertFalse($input->isAllowAdd());
    }

    #[Test]
    public function itChainsAllSetters(): void
    {
        $input = KeyValue::make()
            ->name('config')
            ->label('Config')
            ->required()
            ->keyPlaceholder('Parameter')
            ->valuePlaceholder('Value')
            ->allowDelete(true)
            ->allowAdd(true);

        $this->assertSame('config', $input->getName());
        $this->assertSame('Config', $input->getLabel());
        $this->assertTrue($input->isRequired());
        $this->assertSame('Parameter', $input->getKeyPlaceholder());
        $this->assertSame('Value', $input->getValuePlaceholder());
        $this->assertTrue($input->isAllowDelete());
        $this->assertTrue($input->isAllowAdd());
    }
}
