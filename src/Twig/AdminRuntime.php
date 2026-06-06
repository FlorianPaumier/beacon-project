<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Twig;

use Devgeek\BeaconAdmin\Controller\AbstractDashboardController;
use Devgeek\BeaconAdmin\Menu\MenuBuilder;
use Devgeek\BeaconAdmin\Widget\WidgetRegistry;
use Symfony\Component\HttpFoundation\RequestStack;

class AdminRuntime
{
    /** @var array<string, mixed> */
    protected array $config;

    protected ?MenuBuilder $menuBuilder;

    protected ?WidgetRegistry $widgetRegistry;

    protected ?RequestStack $requestStack;

    protected ?AbstractDashboardController $dashboardController;

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(
        array $config = [],
        ?MenuBuilder $menuBuilder = null,
        ?WidgetRegistry $widgetRegistry = null,
        ?RequestStack $requestStack = null,
        ?AbstractDashboardController $dashboardController = null,
    ) {
        $this->config = $config;
        $this->menuBuilder = $menuBuilder;
        $this->widgetRegistry = $widgetRegistry;
        $this->requestStack = $requestStack;
        $this->dashboardController = $dashboardController;
    }

    public function getDashboardController(): ?AbstractDashboardController
    {
        return $this->dashboardController;
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

    public function getTitle(): string
    {
        if ($this->dashboardController) {
            $title = $this->dashboardController->configureTitle();

            if (null !== $title) {
                return $title;
            }
        }

        return $this->getConfig('title') ?? 'Beacon Admin';
    }

    public function getTheme(): string
    {
        if ($this->dashboardController) {
            $theme = $this->dashboardController->configureDefaultTheme();

            if (null !== $theme) {
                return $theme;
            }
        }

        return $this->getConfig('default_theme') ?? 'modern';
    }

    /** @return array{name: string, logo_path: ?string, favicon_path: ?string, primary_color: string, accent_color: string, support_email: ?string} */
    public function getBrand(): array
    {
        $defaults = [
            'name' => 'Beacon Admin',
            'logo_path' => null,
            'favicon_path' => null,
            'primary_color' => '#2563eb',
            'accent_color' => '#0ea5e9',
            'support_email' => null,
        ];

        $brand = $this->getConfig('brand');
        if (!is_array($brand)) {
            $brand = [];
        }

        // Controller config takes precedence
        if ($this->dashboardController) {
            $controllerBrand = $this->dashboardController->configureBrand();
            $brand = array_merge($brand, $controllerBrand);
        }

        return [
            'name' => (string) ($brand['name'] ?? $defaults['name']),
            'logo_path' => isset($brand['logo_path']) ? (string) $brand['logo_path'] : $defaults['logo_path'],
            'favicon_path' => isset($brand['favicon_path']) ? (string) $brand['favicon_path'] : $defaults['favicon_path'],
            'primary_color' => (string) ($brand['primary_color'] ?? $defaults['primary_color']),
            'accent_color' => (string) ($brand['accent_color'] ?? $defaults['accent_color']),
            'support_email' => isset($brand['support_email']) ? (string) $brand['support_email'] : $defaults['support_email'],
        ];
    }

    /** @return array<string, string> */
    public function getThemes(): array
    {
        if ($this->dashboardController) {
            $themes = $this->dashboardController->configureThemes();

            if ([] !== $themes) {
                return $themes;
            }
        }

        $themes = $this->getConfig('themes');

        return is_array($themes) ? $themes : [];
    }

    /** @return array<array{label: string, route: ?string, icon: ?string, role: ?string, children: array<mixed>}> */
    public function getMenu(): array
    {
        if ($this->dashboardController) {
            $items = $this->dashboardController->configureMenuItems();

            if ([] !== $items) {
                $currentRoute = $this->requestStack?->getCurrentRequest()?->attributes->get('_route');

                return $this->buildMenuTree($items, $currentRoute);
            }
        }

        $items = $this->getConfig('menu');

        if (!is_array($items)) {
            return [];
        }

        $currentRoute = $this->requestStack?->getCurrentRequest()?->attributes->get('_route');

        return $this->buildMenuTree($items, $currentRoute);
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
     * @return array<array{label: string, route: ?string, icon: ?string, role: ?string, children: array<mixed>, active: bool}>
     */
    private function buildMenuTree(array $items, ?string $currentRoute): array
    {
        $tree = [];

        foreach ($items as $item) {
            $children = [];

            $itemChildren = $item['children'] ?? null;

            if (is_array($itemChildren)) {
                $children = $this->buildMenuTree($itemChildren, $currentRoute);
            }

            $itemRoute = $item['route'] ?? null;
            $active = $itemRoute !== null && $itemRoute === $currentRoute;

            $tree[] = [
                'label' => $item['label'],
                'route' => $itemRoute,
                'icon' => $item['icon'] ?? null,
                'role' => $item['role'] ?? null,
                'children' => $children,
                'active' => $active,
            ];
        }

        return $tree;
    }
}
