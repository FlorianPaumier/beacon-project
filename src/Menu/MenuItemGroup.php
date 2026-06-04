<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Menu;

class MenuItemGroup implements MenuItemInterface
{
    protected string $label;

    protected ?string $icon = null;

    /** @var array<MenuItemInterface> */
    protected array $children = [];

    public static function make(): self
    {
        return new self();
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

    public function icon(?string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    /** @param array<MenuItemInterface> $children */
    public function children(array $children): static
    {
        $this->children = $children;

        return $this;
    }

    /** @return array<MenuItemInterface> */
    public function getChildren(): array
    {
        return $this->children;
    }

    public function hasChildren(): bool
    {
        return $this->children !== [];
    }

    public function getRoute(): ?string
    {
        return null;
    }

    public function getRole(): ?string
    {
        return null;
    }

    public function matchesRoute(string $currentRoute): bool
    {
        return false;
    }
}
