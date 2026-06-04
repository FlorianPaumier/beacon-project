<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Fixtures\TestApp\Controller;

use Devgeek\BeaconAdmin\Controller\AbstractCrudController;
use Devgeek\BeaconAdmin\Crud\CrudConfig;
use Devgeek\BeaconAdmin\Tests\Fixtures\TestApp\Entity\ClonePost;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/clone-posts', name: 'clone_post_')]
final class ClonePostCrudController extends AbstractCrudController
{
    protected function configureCrud(CrudConfig $config): void
    {
        $config
            ->entityLabel('Clone Post')
            ->entityLabelPlural('Clone Posts')
            ->fields(['name', 'slug'])
            ->sortableFields(['name', 'slug'])
            ->searchableFields(['name', 'slug'])
            ->pageSize(25);
    }

    protected function getEntityClass(): string
    {
        return ClonePost::class;
    }
}
