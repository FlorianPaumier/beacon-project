<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Menu;

use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

final class MenuBuilder
{
    /** @var callable[] */
    private array $extensions = [];

    /** @var MenuItem[] */
    private array $items = [];

    public function __construct(
        private readonly ?AuthorizationCheckerInterface $checker = null,
    ) {
    }

    public function addExtension(callable $extension): void
    {
        $this->extensions[] = $extension;
    }

    /** @param MenuItem[] $items */
    public function setItems(array $items): void
    {
        $this->items = $items;
    }

    /** @return MenuItem[] */
    public function build(): array
    {
        $items = $this->items;

        foreach ($this->extensions as $extension) {
            $items = $extension($items);
        }

        return $this->filterByRole($items);
    }

    /** @param MenuItem[] $items */
    private function filterByRole(array $items): array
    {
        if (null === $this->checker) {
            return $items;
        }

        return array_values(array_filter(
            $items,
            fn (MenuItem $item): bool => $this->isAccessible($item),
        ));
    }

    private function isAccessible(MenuItem $item): bool
    {
        if (null !== $item->role && ! $this->checker->isGranted($item->role)) {
            return false;
        }

        if ($item->hasChildren()) {
            $accessible = array_filter(
                $item->children,
                fn (MenuItem $child): bool => $this->isAccessible($child),
            );

            return [] !== $accessible;
        }

        return true;
    }
}
