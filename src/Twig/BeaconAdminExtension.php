<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class BeaconAdminExtension extends AbstractExtension
{
    /** @return TwigFunction[] */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('beacon_config', [$this, 'beaconConfig']),
            new TwigFunction('beacon_theme', [$this, 'themeConfig']),
        ];
    }

    /** Returns the full beacon_admin.* parameter bag for templates. */
    public function beaconConfig(): array
    {
        // Parameters are injected via container; use a runtime for actual impl
        return [];
    }

    /** Returns theme-specific values (primary color, dark mode enabled). */
    public function themeConfig(): array
    {
        return [
            'primary_color' => '#2563eb',
            'dark_mode' => true,
        ];
    }
}
