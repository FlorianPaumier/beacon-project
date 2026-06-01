<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Devgeek\BeaconAdmin\Controller\DashboardController;
use Devgeek\BeaconAdmin\Menu\MenuBuilder;
use Devgeek\BeaconAdmin\Twig\BeaconAdminExtension;
use Devgeek\BeaconAdmin\Widget\WidgetRegistry;

return static function (ContainerConfigurator $container): void {
    $services = $container->services()
        ->defaults()
            ->autowire()
            ->autoconfigure()
    ;

    // Controllers
    $services
        ->load('Devgeek\\BeaconAdmin\\Controller\\', '../src/Controller/')
        ->tag('controller.service_arguments')
    ;

    // Core services
    $services
        ->set(MenuBuilder::class)
            ->public()
    ;

    $services
        ->set(WidgetRegistry::class)
            ->public()
    ;

    $services
        ->set(BeaconAdminExtension::class)
            ->tag('twig.extension')
    ;

    // Auto-register everything under src/ except excluded paths
    $services
        ->load('Devgeek\\BeaconAdmin\\', '../src/')
        ->exclude([
            '../src/DependencyInjection/',
            '../src/BeaconAdminBundle.php',
        ])
    ;
};
