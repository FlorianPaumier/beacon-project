<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class BreadcrumbExtension extends AbstractExtension
{
    /** @return array<TwigFunction> */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('beacon_admin_breadcrumbs', [BreadcrumbRenderer::class, 'build']),
        ];
    }
}
