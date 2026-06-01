<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Menu;

class MenuItem
{
    protected string $label;

    protected ?string $route = null;

    protected ?string $icon = null;

    protected ?string $role = null;

    /** @var array<MenuItem> */
    protected array $children = [];

    public static function make(): static
    {
        return new static();
    }

    public function label(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function route(?string $route): static
    {
        $this->route = $route;

        return $this;
    }

    public function getRoute(): ?string
    {
        return $this->route;
    }

    public function icon(?string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function role(?string $role): static
    {
        $this->role = $role;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    /** @param array<MenuItem> $children */
    public function children(array $children): static
    {
        $this->children = $children;

        return $this;
    }

    /** @return array<MenuItem> */
    public function getChildren(): array
    {
        return $this->children;
    }

    public function hasChildren(): bool
    {
        return [] !== $this->children;
    }
}
