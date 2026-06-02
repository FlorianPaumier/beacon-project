# Beacon Admin

A lightweight, modern admin dashboard bundle for Symfony applications.

## Features

- Dashboard with configurable widget grid
- CRUD resource management with Doctrine ORM integration
- Auto-generated forms from entity metadata
- Server-side datatables with search, sort, and pagination
- Configurable sidebar navigation
- Dark/light theme support
- Authentication integration (Symfony Security)
- Responsive layout (mobile + desktop)
- Flash notifications

## Installation

```bash
composer require devgeek/beacon-admin
```

## Quick Start

1. Add configuration in `config/packages/beacon_admin.yaml`:

```yaml
beacon_admin:
    route_prefix: /admin
    title: 'My Admin'
    menu:
        - { label: Dashboard, route: beacon_admin_dashboard, icon: home }
```

2. Import routes in `config/routes/beacon_admin.yaml`:

```yaml
beacon_admin:
    resource: '@BeaconAdminBundle/config/routes/beacon_admin.yaml'
```

3. Create a CRUD controller:

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
            ->fields(['name', 'price', 'category', 'createdAt'])
            ->sortableFields(['name', 'price', 'createdAt'])
            ->searchableFields(['name'])
            ->pageSize(25);
    }
}
```

## Documentation

See the `docs/` directory:

- [Getting Started](docs/getting-started.md)
- [Configuration Reference](docs/configuration.md)
- [CRUD Engine Guide](docs/crud.md)
- [Widget Development](docs/widgets.md)
- [Theming Guide](docs/theming.md)

## Requirements

- PHP 8.4+
- Symfony 7.4 / 8.0
- Doctrine ORM

## License

MIT
