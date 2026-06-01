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
            new TwigFunction('beacon_config', [$this, 'beaconConfig']),
            new TwigFunction('beacon_theme', [$this, 'themeConfig']),
        ];
    }

    public static function make(): self
    {
        return new self();
    }

    /** @return array<string, mixed> */
    public function beaconConfig(): array
    {
        return [];
    }

    /** @return array<string, mixed> */
    public function themeConfig(): array
    {
        return [
            'primary_color' => '#2563eb',
            'dark_mode' => true,
        ];
    }
}
