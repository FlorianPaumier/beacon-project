<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\User;
use Devgeek\BeaconAdmin\Controller\AbstractCrudController;
use Devgeek\BeaconAdmin\Crud\CrudConfig;
use Devgeek\BeaconAdmin\Security\BeaconAccess;
use Symfony\Component\Routing\Attribute\Route;

#[BeaconAccess(role: 'ROLE_ADMIN')]
#[Route('/admin/users', name: 'beacon_admin_demo_user_')]
final class UserCrudController extends AbstractCrudController
{
    protected function getEntityClass(): string
    {
        return User::class;
    }

    protected function configureCrud(CrudConfig $config): void
    {
        $config
            ->fields(['name', 'email', 'isActive', 'createdAt'])
            ->sortableFields(['name', 'email', 'createdAt'])
            ->searchableFields(['name', 'email'])
            ->pageSize(25);
    }
}
