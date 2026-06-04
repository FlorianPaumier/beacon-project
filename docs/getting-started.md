# Getting Started with Beacon Admin

> **Docs:** Getting Started | [Configuration →](configuration.md) | [CRUD Guide](crud.md) | [Widgets](widgets.md) | [Theming](theming.md)

## Installing

```bash
composer require devgeek/beacon-admin
```

## Configuring

Create `config/packages/beacon_admin.yaml`:

```yaml
beacon_admin:
    route_prefix: /admin
    title: 'My Admin Panel'
    menu:
        - { label: Dashboard, route: beacon_admin_dashboard, icon: home }
        - { label: Products, route: beacon_admin_product_list, icon: box }
    default_theme: modern
    security:
        admin_role: ROLE_ADMIN
```

## Adding Routes

Import the bundle routes in `config/routes/beacon_admin.yaml`:

```yaml
beacon_admin:
    resource: '@BeaconAdminBundle/config/routes/beacon_admin.yaml'
```

## Creating a CRUD Resource

Create a controller that extends `AbstractCrudController`:

```php
<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Product;
use Devgeek\BeaconAdmin\Controller\AbstractCrudController;
use Devgeek\BeaconAdmin\Crud\CrudConfig;

final class ProductCrudController extends AbstractCrudController
{
    protected function getEntityClass(): string
    {
        return Product::class;
    }

    protected function configureCrud(CrudConfig $config): void
    {
        $config
            ->fields(['name', 'price', 'category', 'isActive', 'createdAt'])
            ->sortableFields(['name', 'price', 'createdAt'])
            ->searchableFields(['name'])
            ->pageSize(25);
    }
}
```

## Running

```bash
symfony serve
```

Visit `/admin` to see your dashboard.

## Next Steps

- [Configuration Reference](configuration.md)
- [CRUD Engine Guide](crud.md)
- [Widget Development](widgets.md)
- [Theming Guide](theming.md)
