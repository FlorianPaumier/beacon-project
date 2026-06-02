<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Menu;

use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class MenuBuilder
{
    /** @var array<MenuItemInterface> */
    protected array $items = [];

    /** @var array<callable> */
    protected array $extensions = [];

    protected ?AuthorizationCheckerInterface $checker;

    public static function make(): self
    {
        return new self();
    }

    public function __construct(?AuthorizationCheckerInterface $checker = null)
    {
        $this->checker = $checker;
    }

    public function checker(?AuthorizationCheckerInterface $checker): static
    {
        $this->checker = $checker;

        return $this;
    }

    public function getChecker(): ?AuthorizationCheckerInterface
    {
        return $this->checker;
    }

    /** @param array<MenuItemInterface> $items */
    public function items(array $items): static
    {
        $this->items = $items;

        return $this;
    }

    /** @return array<MenuItemInterface> */
    public function getItems(): array
    {
        return $this->items;
    }

    public function addExtension(callable $extension): static
    {
        $this->extensions[] = $extension;

        return $this;
    }

    /** @return array<MenuItemInterface> */
    public function build(): array
    {
        $items = $this->items;

        foreach ($this->extensions as $extension) {
            $items = $extension($items);
        }

        return $this->filterByRole($items);
    }

    /**
     * @param array<MenuItemInterface> $items
     *
     * @return array<MenuItemInterface>
     */
    private function filterByRole(array $items): array
    {
        if ($this->checker === null) {
            return $items;
        }

        return array_values(array_filter(
            $items,
            fn (MenuItemInterface $item): bool => $this->isAccessible($item),
        ));
    }

    private function isAccessible(MenuItemInterface $item): bool
    {
        if ($this->checker !== null && $item->getRole() !== null && !$this->checker->isGranted($item->getRole())) {
            return false;
        }

        if ($item->hasChildren()) {
            $accessible = array_filter(
                $item->getChildren(),
                fn (MenuItemInterface $child): bool => $this->isAccessible($child),
            );

            return $accessible !== [];
        }

        return true;
    }
}
