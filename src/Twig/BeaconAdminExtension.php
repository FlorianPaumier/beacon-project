<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class BeaconAdminExtension extends AbstractExtension
{
    /** @return array<TwigFunction> */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('beacon_admin_config', [AdminRuntime::class, 'getConfig']),
            new TwigFunction('beacon_admin_theme', [AdminRuntime::class, 'getTheme']),
            new TwigFunction('beacon_admin_themes', [AdminRuntime::class, 'getThemes']),
            new TwigFunction('beacon_admin_menu', [AdminRuntime::class, 'getMenu']),
            new TwigFunction('beacon_admin_widgets', [AdminRuntime::class, 'getWidgets']),
            new TwigFunction('beacon_admin_brand', [AdminRuntime::class, 'getBrand']),
        ];
    }

    public static function make(): self
    {
        return new self();
    }
}
