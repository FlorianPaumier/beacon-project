<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Widget;

final class WidgetRegistry
{
    /** @var array<string, DashboardWidgetInterface> */
    private array $widgets = [];

    public function register(DashboardWidgetInterface $widget): void
    {
        $this->widgets[$widget->getName()] = $widget;
    }

    /** @return DashboardWidgetInterface[] sorted by priority */
    public function all(): array
    {
        $widgets = array_values($this->widgets);

        usort($widgets, static fn (DashboardWidgetInterface $a, DashboardWidgetInterface $b): int => $a->getPriority() <=> $b->getPriority());

        return $widgets;
    }

    public function get(string $name): ?DashboardWidgetInterface
    {
        return $this->widgets[$name] ?? null;
    }
}
