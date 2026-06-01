<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin;

use Devgeek\BeaconAdmin\DependencyInjection\Compiler\MenuPass;
use Devgeek\BeaconAdmin\DependencyInjection\Compiler\WidgetPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

final class BeaconAdminBundle extends AbstractBundle
{
    public function loadExtension(
        array $config,
        ContainerConfigurator $container,
        ContainerBuilder $builder,
    ): void {
        $container->import('../config/services.php');

        $container->parameters()
            ->set('beacon_admin.route_prefix', $config['route_prefix'])
            ->set('beacon_admin.title', $config['title'])
        ;

        if (isset($config['theme'])) {
            $container->parameters()
                ->set('beacon_admin.theme.primary_color', $config['theme']['primary_color'])
                ->set('beacon_admin.theme.dark_mode', $config['theme']['dark_mode'])
            ;
        }

        $container->parameters()
            ->set('beacon_admin.menu.items', $config['menu'] ?? [])
        ;
    }

    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new WidgetPass());
        $container->addCompilerPass(new MenuPass());
    }

    public function configureRoutes(RoutingConfigurator $routes): void
    {
        $routes->import('../src/Controller/', 'attribute');
    }
}
