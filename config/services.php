<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Devgeek\BeaconAdmin\Menu\MenuBuilder;
use Devgeek\BeaconAdmin\Twig\BeaconAdminExtension;
use Devgeek\BeaconAdmin\Widget\WidgetRegistry;

return static function (ContainerConfigurator $container): void {
    $services = $container->services()
        ->defaults()
            ->autowire()
            ->autoconfigure()
    ;

    // Auto-register everything under src/
    // Controllers get #[AsController] auto-tagging via autoconfigure
    $services
        ->load('Devgeek\\BeaconAdmin\\', '../src/')
        ->exclude([
            '../src/DependencyInjection/',
            '../src/BeaconAdminBundle.php',
        ])
    ;

    // Services made public for direct container access (tests, menu building)
    $services->set(MenuBuilder::class)->public();
    $services->set(WidgetRegistry::class)->public();

    // Twig extension
    $services->set(BeaconAdminExtension::class)->tag('twig.extension');
};
