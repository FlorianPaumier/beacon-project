<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Menu;

interface MenuItemInterface
{
    public function getLabel(): string;

    public function getRoute(): ?string;

    public function getIcon(): ?string;

    public function getRole(): ?string;

    /** @return array<MenuItemInterface> */
    public function getChildren(): array;

    public function hasChildren(): bool;

    public function matchesRoute(string $currentRoute): bool;
}
