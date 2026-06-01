<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

final class BeaconAdminExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new PhpFileLoader($container, new FileLocator(\dirname(__DIR__, 2) . '/config'));
        $loader->load('services.php');

        $container->setParameter('beacon_admin.route_prefix', $config['route_prefix']);
        $container->setParameter('beacon_admin.title', $config['title']);
        $container->setParameter('beacon_admin.theme.primary_color', $config['theme']['primary_color']);
        $container->setParameter('beacon_admin.theme.dark_mode', $config['theme']['dark_mode']);
        $container->setParameter('beacon_admin.menu.items', $config['menu']);
    }

    public function getAlias(): string
    {
        return 'beacon_admin';
    }
}
