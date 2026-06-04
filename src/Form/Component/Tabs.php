<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Form\Component;

class Tabs
{
    /** @var array<TabPane> */
    private array $panes = [];

    public static function make(): self
    {
        return new self();
    }

    public function add(TabPane $pane): self
    {
        $this->panes[] = $pane;

        return $this;
    }

    /** @return array<TabPane> */
    public function getPanes(): array
    {
        return $this->panes;
    }
}
