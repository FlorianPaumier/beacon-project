<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Form\Component;

class TabPane
{
    private string $label;
    /** @var array<Row|Column> */
    private array $children = [];

    public static function make(string $label): self
    {
        $pane = new self();
        $pane->label = $label;

        return $pane;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    /** @param array<Row|Column> $children */
    public function children(array $children): self
    {
        $this->children = $children;

        return $this;
    }

    /** @return array<Row|Column> */
    public function getChildren(): array
    {
        return $this->children;
    }
}
