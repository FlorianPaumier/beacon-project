<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Crud\DataTable\Column;

use Devgeek\BeaconAdmin\Crud\DataTable\Column\ActionsColumn;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ActionsColumnTest extends TestCase
{
    #[Test]
    public function itCreatesViaMakeWithDefaultName(): void
    {
        $column = ActionsColumn::make();

        $this->assertInstanceOf(ActionsColumn::class, $column);
        $this->assertSame('__actions', $column->getName());
        $this->assertSame('Actions', $column->getLabel());
    }

    #[Test]
    public function itSetsEditRoute(): void
    {
        $column = ActionsColumn::make()->editRoute('admin_edit');

        $this->assertSame('admin_edit', $column->getEditRoute());
    }

    #[Test]
    public function itSetsDeleteRoute(): void
    {
        $column = ActionsColumn::make()->deleteRoute('admin_delete');

        $this->assertSame('admin_delete', $column->getDeleteRoute());
    }

    #[Test]
    public function itDefaultsEditRouteToEmpty(): void
    {
        $column = ActionsColumn::make();

        $this->assertSame('', $column->getEditRoute());
    }

    #[Test]
    public function itDefaultsDeleteRouteToEmpty(): void
    {
        $column = ActionsColumn::make();

        $this->assertSame('', $column->getDeleteRoute());
    }

    #[Test]
    public function itChainsBothRoutes(): void
    {
        $column = ActionsColumn::make()
            ->editRoute('admin_edit')
            ->deleteRoute('admin_delete');

        $this->assertSame('admin_edit', $column->getEditRoute());
        $this->assertSame('admin_delete', $column->getDeleteRoute());
    }
}
