<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Twig;

use Devgeek\BeaconAdmin\Twig\AdminRuntime;
use Devgeek\BeaconAdmin\Widget\WidgetRegistry;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class AdminRuntimeTest extends TestCase
{
    #[Test]
    public function itReturnsFullConfigWhenNoKeyGiven(): void
    {
        $config = ['default_theme' => 'modern', 'menu' => []];
        $runtime = new AdminRuntime($config);

        $this->assertSame($config, $runtime->getConfig(null));
    }

    #[Test]
    public function itReturnsDotNotationConfigValue(): void
    {
        $config = ['themes' => ['modern' => 'modern.css']];
        $runtime = new AdminRuntime($config);

        $this->assertSame('modern.css', $runtime->getConfig('themes.modern'));
    }

    #[Test]
    public function itReturnsNullForUnknownDotNotationKey(): void
    {
        $runtime = new AdminRuntime([]);

        $this->assertNull($runtime->getConfig('nonexistent.key'));
    }

    #[Test]
    public function itReturnsDefaultThemeFromConfig(): void
    {
        $runtime = new AdminRuntime(['default_theme' => 'enterprise']);

        $this->assertSame('enterprise', $runtime->getTheme());
    }

    #[Test]
    public function itReturnsModernAsFallbackTheme(): void
    {
        $runtime = new AdminRuntime([]);

        $this->assertSame('modern', $runtime->getTheme());
    }

    #[Test]
    public function itReturnsEmptyMenuWhenNoConfig(): void
    {
        $runtime = new AdminRuntime([]);

        $this->assertSame([], $runtime->getMenu());
    }

    #[Test]
    public function itBuildsMenuTreeFromConfig(): void
    {
        $config = [
            'menu' => [
                ['label' => 'Dashboard', 'route' => 'dashboard'],
                ['label' => 'Settings', 'route' => 'settings', 'icon' => 'fas fa-cog', 'role' => 'ROLE_ADMIN',
                    'children' => [
                        ['label' => 'Profile', 'route' => 'profile'],
                    ],
                ],
            ],
        ];
        $runtime = new AdminRuntime($config);

        $menu = $runtime->getMenu();

        $this->assertCount(2, $menu);
        $this->assertSame('Dashboard', $menu[0]['label']);
        $this->assertSame('dashboard', $menu[0]['route']);
        $this->assertNull($menu[0]['icon']);
        $this->assertNull($menu[0]['role']);
        $this->assertSame([], $menu[0]['children']);

        $this->assertSame('Settings', $menu[1]['label']);
        $this->assertSame('settings', $menu[1]['route']);
        $this->assertSame('fas fa-cog', $menu[1]['icon']);
        $this->assertSame('ROLE_ADMIN', $menu[1]['role']);
        $this->assertCount(1, $menu[1]['children']);
        $this->assertSame('Profile', $menu[1]['children'][0]['label']);
    }

    #[Test]
    public function itReturnsEmptyWidgetsWhenNoRegistry(): void
    {
        $runtime = new AdminRuntime([]);

        $this->assertSame([], $runtime->getWidgets());
    }

    #[Test]
    public function itReturnsWidgetsFromRegistry(): void
    {
        $widget = $this->createWidget('stats');
        $registry = WidgetRegistry::make();
        $registry->register($widget);
        $runtime = new AdminRuntime([], null, $registry);

        $widgets = $runtime->getWidgets();
        $this->assertCount(1, $widgets);
        $this->assertSame($widget, $widgets[0]);
    }

    private function createWidget(string $name): \Devgeek\BeaconAdmin\Widget\DashboardWidgetInterface
    {
        return new readonly class($name) implements \Devgeek\BeaconAdmin\Widget\DashboardWidgetInterface {
            public function __construct(private string $name)
            {
            }

            public function getName(): string
            {
                return $this->name;
            }

            public function getLabel(): string
            {
                return $this->name;
            }

            public function getCols(): int
            {
                return 6;
            }

            public function getPriority(): int
            {
                return 0;
            }

            public function render(): string
            {
                return '';
            }
        };
    }
}
