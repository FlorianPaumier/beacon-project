<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin;

use Devgeek\BeaconAdmin\DependencyInjection\Compiler\MenuPass;
use Devgeek\BeaconAdmin\DependencyInjection\Compiler\WidgetPass;
use Devgeek\BeaconAdmin\Security\BeaconAccessVoter;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class BeaconAdminBundle extends AbstractBundle
{
    /**
     * Hook: defines the semantic configuration tree.
     * Replaces the standalone Configuration class.
     */
    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()
            ->children()
                ->scalarNode('route_prefix')
                    ->defaultValue('/admin')
                    ->info('URL prefix for all admin routes')
                ->end()
                ->scalarNode('title')
                    ->defaultValue('Beacon Admin')
                    ->info('Title shown in header and <title> tag')
                ->end()
                ->scalarNode('logo')
                    ->defaultNull()
                    ->info('Path to logo image')
                ->end()
                ->arrayNode('theme')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('primary_color')->defaultValue('#2563eb')->end()
                        ->booleanNode('dark_mode')->defaultTrue()->end()
                    ->end()
                ->end()
                ->arrayNode('menu')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('label')->isRequired()->end()
                            ->scalarNode('route')->end()
                            ->scalarNode('icon')->end()
                            ->scalarNode('role')->end()
                            ->arrayNode('children')
                                ->arrayPrototype()
                                    ->children()
                                        ->scalarNode('label')->isRequired()->end()
                                        ->scalarNode('route')->isRequired()->end()
                                        ->scalarNode('icon')->end()
                                        ->scalarNode('role')->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('security')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('role')->defaultValue('ROLE_ADMIN')->end()
                        ->booleanNode('voters')->defaultTrue()->end()
                    ->end()
                ->end()
                ->arrayNode('assets')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('css_framework')->defaultValue('custom')->end()
                    ->end()
                ->end()
            ->end();
    }

    /**
     * Hook: loads services and processes merged configuration.
     * Replaces the standalone Extension class.
     */
    public function loadExtension(
        array $config,
        ContainerConfigurator $container,
        ContainerBuilder $builder,
    ): void {
        $loader = new PhpFileLoader($builder, new FileLocator(__DIR__.'/../config'));
        $loader->load('services.php');

        $builder->setParameter('beacon_admin.route_prefix', $config['route_prefix']);
        $builder->setParameter('beacon_admin.title', $config['title']);
        $builder->setParameter('beacon_admin.theme.primary_color', $config['theme']['primary_color']);
        $builder->setParameter('beacon_admin.theme.dark_mode', $config['theme']['dark_mode']);
        $builder->setParameter('beacon_admin.menu.items', $config['menu']);
        $builder->setParameter('beacon_admin.security.role', $config['security']['role']);

        if ((bool) $config['security']['voters']) {
            $builder->findDefinition(BeaconAccessVoter::class)
                ->setArgument('$adminRole', $config['security']['role']);
        } else {
            $builder->removeDefinition(BeaconAccessVoter::class);
        }
    }

    /**
     * Hook: register compiler passes.
     */
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new WidgetPass());
        $container->addCompilerPass(new MenuPass());
    }
}
