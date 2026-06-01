<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Widget;

interface DashboardWidgetInterface
{
    /** Returns the unique name for this widget (used as template identifier). */
    public function getName(): string;

    /** Returns the display label shown in the widget header. */
    public function getLabel(): string;

    /** Returns the grid column span (1-12). Default: 6 (half width). */
    public function getCols(): int;

    /** Returns the rendered widget HTML. */
    public function render(): string;

    /** Returns the widget priority (lower = shown first). Default: 0. */
    public function getPriority(): int;
}
