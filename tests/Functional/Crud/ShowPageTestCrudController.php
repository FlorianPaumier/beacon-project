<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Functional\Crud;

use Devgeek\BeaconAdmin\Controller\AbstractCrudController;
use Devgeek\BeaconAdmin\Crud\CrudConfig;
use Devgeek\BeaconAdmin\Crud\DataTable\Column\BooleanColumn;
use Devgeek\BeaconAdmin\Crud\DataTable\Column\TextColumn;
use Devgeek\BeaconAdmin\Tests\Fixtures\TestApp\Entity\TestEntity;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

/** @internal */
#[AsController]
#[Route('/admin/show-test', name: 'show_test_')]
final class ShowPageTestCrudController extends AbstractCrudController
{
    protected function configureCrud(CrudConfig $config): void
    {
        $config
            ->entityLabel('Test Entity')
            ->entityLabelPlural('Test Entities')
            ->fields(['id', 'name', 'email'])
            ->sortableFields(['id', 'name'])
            ->searchableFields(['name', 'email'])
            ->pageSize(10)
            ->column(TextColumn::make('name')->label('Name'))
            ->column(TextColumn::make('email')->label('Email'))
            ->column(BooleanColumn::make('active')->label('Active')->trueLabel('Yes')->falseLabel('No'));
    }

    protected function getEntityClass(): string
    {
        return TestEntity::class;
    }
}
