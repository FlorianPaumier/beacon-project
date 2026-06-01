<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Event;

use Devgeek\BeaconAdmin\Widget\DashboardWidgetInterface;

final class DashboardBuiltEvent
{
    /** @param DashboardWidgetInterface[] $widgets */
    public function __construct(
        private array $widgets,
    ) {
    }

    /** @return DashboardWidgetInterface[] */
    public function getWidgets(): array
    {
        return $this->widgets;
    }

    public function addWidget(DashboardWidgetInterface $widget): void
    {
        $this->widgets[] = $widget;
    }

    public function removeWidget(string $name): void
    {
        $this->widgets = array_values(array_filter(
            $this->widgets,
            static fn (DashboardWidgetInterface $w): bool => $w->getName() !== $name,
        ));
    }
}
