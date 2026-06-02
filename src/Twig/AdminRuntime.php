<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Twig;

use Devgeek\BeaconAdmin\Menu\MenuBuilder;
use Devgeek\BeaconAdmin\Widget\WidgetRegistry;

class AdminRuntime
{
    /** @var array<string, mixed> */
    protected array $config;

    protected ?MenuBuilder $menuBuilder;

    protected ?WidgetRegistry $widgetRegistry;

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(
        array $config = [],
        ?MenuBuilder $menuBuilder = null,
        ?WidgetRegistry $widgetRegistry = null,
    ) {
        $this->config = $config;
        $this->menuBuilder = $menuBuilder;
        $this->widgetRegistry = $widgetRegistry;
    }

    public function getConfig(?string $key = null): mixed
    {
        if (null === $key || '' === $key) {
            return $this->config;
        }

        $keys = explode('.', $key);
        $value = $this->config;

        foreach ($keys as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return null;
            }

            $value = $value[$segment];
        }

        return $value;
    }

    public function getTheme(): string
    {
        return $this->getConfig('default_theme') ?? 'modern';
    }

    /** @return array<string, string> */
    public function getThemes(): array
    {
        $themes = $this->getConfig('themes');

        return is_array($themes) ? $themes : [];
    }

    /** @return array<array{label: string, route: ?string, icon: ?string, role: ?string, children: array<mixed>}> */
    public function getMenu(): array
    {
        $items = $this->getConfig('menu');

        if (!is_array($items)) {
            return [];
        }

        return $this->buildMenuTree($items);
    }

    /** @return array<array-key, mixed> */
    public function getWidgets(): array
    {
        if (null === $this->widgetRegistry) {
            return [];
        }

        return $this->widgetRegistry->all();
    }

    /**
     * @param array<array{label: string, route?: string, icon?: ?string, role?: ?string, children?: array<mixed>}> $items
     *
     * @return array<array{label: string, route: ?string, icon: ?string, role: ?string, children: array<mixed>}>
     */
    private function buildMenuTree(array $items): array
    {
        $tree = [];

        foreach ($items as $item) {
            $children = [];

            $itemChildren = $item['children'] ?? null;

            if (is_array($itemChildren)) {
                $children = $this->buildMenuTree($itemChildren);
            }

            $tree[] = [
                'label' => $item['label'],
                'route' => $item['route'] ?? null,
                'icon' => $item['icon'] ?? null,
                'role' => $item['role'] ?? null,
                'children' => $children,
            ];
        }

        return $tree;
    }
}
