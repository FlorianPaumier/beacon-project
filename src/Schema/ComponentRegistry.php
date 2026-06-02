<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Schema;

use Devgeek\BeaconAdmin\Support\Component;

class ComponentRegistry
{
    /** @var array<string, class-string<Component>> */
    protected array $components = [];

    /** @param class-string<Component> $component */
    public function register(string $name, string $component): static
    {
        $this->components[$name] = $component;

        return $this;
    }

    /** @return class-string<Component>|null */
    public function get(string $name): ?string
    {
        return $this->components[$name] ?? null;
    }

    /** @return array<string, class-string<Component>> */
    public function all(): array
    {
        return $this->components;
    }
}
