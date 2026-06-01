<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Event;

use Devgeek\BeaconAdmin\Widget\DashboardWidgetInterface;

class DashboardBuiltEvent
{
    /** @var array<DashboardWidgetInterface> */
    protected array $widgets = [];

    /** @param array<DashboardWidgetInterface> $widgets */
    public static function make(array $widgets = []): self
    {
        $event = new self();
        $event->widgets = $widgets;

        return $event;
    }

    /** @return array<DashboardWidgetInterface> */
    public function getWidgets(): array
    {
        return $this->widgets;
    }

    /** @param array<DashboardWidgetInterface> $widgets */
    public function widgets(array $widgets): static
    {
        $this->widgets = $widgets;

        return $this;
    }

    public function addWidget(DashboardWidgetInterface $widget): static
    {
        $this->widgets[] = $widget;

        return $this;
    }

    public function removeWidget(string $name): static
    {
        $this->widgets = array_values(array_filter(
            $this->widgets,
            static fn (DashboardWidgetInterface $w): bool => $w->getName() !== $name,
        ));

        return $this;
    }
}
