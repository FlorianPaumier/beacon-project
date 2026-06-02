<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Product;
use Devgeek\BeaconAdmin\Controller\AbstractCrudController;
use Devgeek\BeaconAdmin\Crud\CrudConfig;
use Devgeek\BeaconAdmin\Security\BeaconAccess;
use Symfony\Component\Routing\Attribute\Route;

#[BeaconAccess(role: 'ROLE_ADMIN')]
#[Route('/admin/products', name: 'beacon_admin_demo_product_')]
final class ProductCrudController extends AbstractCrudController
{
    protected function getEntityClass(): string
    {
        return Product::class;
    }

    protected function configureCrud(CrudConfig $config): void
    {
        $config
            ->fields(['name', 'price', 'description', 'category', 'isActive', 'createdAt'])
            ->sortableFields(['name', 'price', 'createdAt'])
            ->searchableFields(['name', 'description'])
            ->pageSize(25);
    }
}
