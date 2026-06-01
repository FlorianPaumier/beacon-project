<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Menu;

final readonly class MenuItem
{
    /** @param MenuItem[] $children */
    public function __construct(
        public string $label,
        public ?string $route = null,
        public ?string $icon = null,
        public ?string $role = null,
        public array $children = [],
    ) {
    }

    public function hasChildren(): bool
    {
        return [] !== $this->children;
    }
}
