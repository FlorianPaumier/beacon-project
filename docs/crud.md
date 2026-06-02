# CRUD Engine Guide

## Overview

The CRUD engine provides automatic list, create, update, and delete views for Doctrine entities. Create a controller extending `AbstractCrudController` and configure it using the `CrudConfig` fluent API.

## Creating a CRUD Controller

```php
<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Product;
use Devgeek\BeaconAdmin\Controller\AbstractCrudController;
use Devgeek\BeaconAdmin\Crud\CrudConfig;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/products', name: 'beacon_admin_product_')]
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
            ->searchableFields(['name', 'description'])
            ->pageSize(25);
    }
}
```

## Configuring Columns and Fields

Use the `CrudConfig` fluent API:

```php
$config
    ->fields(['name', 'email', 'createdAt'])
    ->sortableFields(['name', 'email', 'createdAt'])
    ->searchableFields(['name', 'email'])
    ->pageSize(50);
```

| Method | Description |
|--------|-------------|
| `fields(array)` | List of field names to display in list and form |
| `sortableFields(array)` | Fields that support server-side sorting |
| `searchableFields(array)` | Fields searched by the datatable search box |
| `pageSize(int)` | Records per page (default: 25) |

## Custom Form Fields

Override form fields by hooking into Symfony Form events or decorating the `FormBuilder` service. The bundle auto-detects Doctrine column types and maps them to Symfony Form types:

| Doctrine Type | Form Type |
|---------------|-----------|
| `string` | `TextType` |
| `text` | `TextareaType` |
| `integer` | `IntegerType` |
| `decimal` | `NumberType` |
| `boolean` | `CheckboxType` |
| `datetime` | `DateTimeType` |
| `date` | `DateType` |

## Events

The CRUD engine dispatches Symfony events at each lifecycle point:

| Event | Fired | Namespace |
|-------|-------|-----------|
| `BeforeCreateEvent` | Before persisting a new entity | `Devgeek\BeaconAdmin\Event` |
| `AfterCreateEvent` | After flushing a new entity | `Devgeek\BeaconAdmin\Event` |
| `BeforeUpdateEvent` | Before flushing changes | `Devgeek\BeaconAdmin\Event` |
| `AfterUpdateEvent` | After flushing changes | `Devgeek\BeaconAdmin\Event` |
| `BeforeDeleteEvent` | Before removing an entity | `Devgeek\BeaconAdmin\Event` |
| `AfterDeleteEvent` | After removing an entity | `Devgeek\BeaconAdmin\Event` |

Listening to an event:

```php
<?php

declare(strict_types=1);

namespace App\EventListener;

use Devgeek\BeaconAdmin\Event\AfterCreateEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener]
final class ProductCreatedListener
{
    public function __invoke(AfterCreateEvent $event): void
    {
        $entity = $event->getEntity();
        // Send notification, log activity, etc.
    }
}
```

## Permissions

Use the `#[BeaconAccess]` attribute on your CRUD controller to restrict access:

```php
use Devgeek\BeaconAdmin\Security\BeaconAccess;

#[BeaconAccess(role: 'ROLE_ADMIN')]
#[Route('/admin/products', name: 'beacon_admin_product_')]
final class ProductCrudController extends AbstractCrudController
{
    // ...
}
```

Per-action permissions are also supported:

```php
#[BeaconAccess(role: 'ROLE_SUPER_ADMIN')]
protected function configureCrud(CrudConfig $config): void
{
    // ...
}
```

## Datatable Features

- **Search**: A search box filters results across all `searchableFields`
- **Sorting**: Click column headers to toggle ascending/descending sort
- **Pagination**: Server-side pagination with configurable page size
- **CSRF Protection**: All mutation actions include CSRF token validation
