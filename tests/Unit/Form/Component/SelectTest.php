<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Form\Component;

use Devgeek\BeaconAdmin\Form\Component\Select;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class SelectTest extends TestCase
{
    #[Test]
    public function itCreatesViaMake(): void
    {
        $select = Select::make();

        $this->assertNotNull($select);
    }

    #[Test]
    public function itSetsNameFluently(): void
    {
        $select = Select::make()->name('role');

        $this->assertSame('role', $select->getName());
    }

    #[Test]
    public function itSetsLabelFluently(): void
    {
        $select = Select::make()->name('role')->label('User Role');

        $this->assertSame('User Role', $select->getLabel());
    }

    #[Test]
    public function itEvaluatesClosureLabel(): void
    {
        $select = Select::make()->name('role')->label(fn () => 'Dynamic Role');

        $this->assertSame('Dynamic Role', $select->getLabel());
    }

    #[Test]
    public function itDefaultsRequiredToFalse(): void
    {
        $select = Select::make()->name('role');

        $this->assertFalse($select->isRequired());
    }

    #[Test]
    public function itSetsRequiredFluently(): void
    {
        $select = Select::make()->name('role')->required();

        $this->assertTrue($select->isRequired());
    }

    #[Test]
    public function itReturnsEmptyOptionsByDefault(): void
    {
        $select = Select::make()->name('role');

        $this->assertSame([], $select->getOptions());
    }

    #[Test]
    public function itSetsOptionsFluently(): void
    {
        $options = ['admin' => 'Admin', 'user' => 'User'];
        $select = Select::make()->name('role')->options($options);

        $this->assertSame($options, $select->getOptions());
    }

    #[Test]
    public function itEvaluatesClosureOptions(): void
    {
        $select = Select::make()->name('role')->options(fn () => ['admin' => 'Admin']);

        $this->assertSame(['admin' => 'Admin'], $select->getOptions());
    }

    #[Test]
    public function itDefaultsMultipleToFalse(): void
    {
        $select = Select::make()->name('role');

        $this->assertFalse($select->isMultiple());
    }

    #[Test]
    public function itSetsMultipleFluently(): void
    {
        $select = Select::make()->name('role')->multiple();

        $this->assertTrue($select->isMultiple());
    }

    #[Test]
    public function itEvaluatesClosureMultiple(): void
    {
        $select = Select::make()->name('role')->multiple(fn () => true);

        $this->assertTrue($select->isMultiple());
    }

    #[Test]
    public function itDefaultsSearchableToFalse(): void
    {
        $select = Select::make()->name('role');

        $this->assertFalse($select->isSearchable());
    }

    #[Test]
    public function itSetsSearchableFluently(): void
    {
        $select = Select::make()->name('role')->searchable();

        $this->assertTrue($select->isSearchable());
    }

    #[Test]
    public function itEvaluatesClosureSearchable(): void
    {
        $select = Select::make()->name('role')->searchable(fn () => true);

        $this->assertTrue($select->isSearchable());
    }

    #[Test]
    public function itChainsAllSetters(): void
    {
        $options = ['active' => 'Active', 'inactive' => 'Inactive'];
        $select = Select::make()
            ->name('status')
            ->label('Status')
            ->required()
            ->options($options)
            ->multiple()
            ->searchable();

        $this->assertSame('status', $select->getName());
        $this->assertSame('Status', $select->getLabel());
        $this->assertTrue($select->isRequired());
        $this->assertSame($options, $select->getOptions());
        $this->assertTrue($select->isMultiple());
        $this->assertTrue($select->isSearchable());
    }
}
