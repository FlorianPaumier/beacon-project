# Widget Development Guide

> **Docs:** [← CRUD Guide](crud.md) | Widgets | [Theming →](theming.md) | [Getting Started](getting-started.md) | [Configuration](configuration.md)

## Overview

Widgets are self-contained dashboard components that display data on the admin dashboard. They implement `DashboardWidgetInterface` and are registered via YAML configuration or service tags.

## Built-in Widgets

### StatsWidget

Displays a single numeric value with an optional label and trend indicator:

```php
use Devgeek\BeaconAdmin\Widget\StatsWidget;

$widget = new StatsWidget(
    label: 'Total Users',
    value: 1247,
    icon: 'people',
    trend: '+12%',
);
```

### TableWidget

Renders a compact table from any Doctrine repository:

```php
use Devgeek\BeaconAdmin\Widget\TableWidget;

$widget = new TableWidget(
    label: 'Recent Orders',
    repository: $entityManager->getRepository(Order::class),
    limit: 5,
    columns: ['id', 'customer', 'total', 'createdAt'],
);
```

## Creating a Custom Widget

Implement `DashboardWidgetInterface`:

```php
<?php

declare(strict_types=1);

namespace App\Admin\Widget;

use Devgeek\BeaconAdmin\Widget\DashboardWidgetInterface;
use Twig\Environment;

final class RecentOrdersWidget implements DashboardWidgetInterface
{
    public function __construct(
        private readonly Environment $twig,
        private readonly \Doctrine\ORM\EntityManagerInterface $entityManager,
    ) {}

    public function getName(): string
    {
        return 'recent_orders';
    }

    public function getLabel(): string
    {
        return 'Recent Orders';
    }

    public function getCols(): int
    {
        return 6;
    }

    public function getPriority(): int
    {
        return 0;
    }

    public function render(): string
    {
        $orders = $this->entityManager
            ->getRepository(\App\Entity\Order::class)
            ->findBy([], ['createdAt' => 'DESC'], 5);

        return $this->twig->render('admin/widgets/recent_orders.html.twig', [
            'orders' => $orders,
        ]);
    }
}
```

## Registering Widgets

### Via YAML Config

```yaml
beacon_admin:
    widgets:
        - App\Admin\Widget\RecentOrdersWidget
```

### Via Service Tag

```yaml
services:
    App\Admin\Widget\RecentOrdersWidget:
        tags:
            - { name: beacon_admin.widget, key: recent_orders }
```

## Widget Grid System

The dashboard uses a 12-column CSS Grid layout. Set `getCols()` to control the width:

| Value | Width |
|-------|-------|
| 12 | Full width |
| 6 | Half width (default) |
| 4 | One third |
| 3 | One quarter |

Widgets wrap to the next row when the grid column total exceeds 12.
