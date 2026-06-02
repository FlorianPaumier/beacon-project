<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Category;
use Devgeek\BeaconAdmin\Controller\AbstractCrudController;
use Devgeek\BeaconAdmin\Crud\CrudConfig;
use Devgeek\BeaconAdmin\Security\BeaconAccess;
use Symfony\Component\Routing\Attribute\Route;

#[BeaconAccess(role: 'ROLE_ADMIN')]
#[Route('/admin/categories', name: 'beacon_admin_demo_category_')]
final class CategoryCrudController extends AbstractCrudController
{
    protected function getEntityClass(): string
    {
        return Category::class;
    }

    protected function configureCrud(CrudConfig $config): void
    {
        $config
            ->fields(['name', 'slug', 'createdAt'])
            ->sortableFields(['name', 'createdAt'])
            ->searchableFields(['name', 'slug'])
            ->pageSize(25);
    }
}
