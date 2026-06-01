<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final readonly class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('beacon_admin');
        $root = $treeBuilder->getRootNode();

        // @formatter:off
        $root
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
                    ->info('Path to logo image (relative to public/, or an absolute URL)')
                ->end()
                ->arrayNode('theme')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('primary_color')
                            ->defaultValue('#2563eb')
                            ->info('Primary brand color (hex)')
                        ->end()
                        ->booleanNode('dark_mode')
                            ->defaultTrue()
                            ->info('Enable dark mode toggle')
                        ->end()
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
                        ->scalarNode('role')
                            ->defaultValue('ROLE_ADMIN')
                            ->info('Role required to access the admin panel')
                        ->end()
                        ->booleanNode('voters')
                            ->defaultTrue()
                            ->info('Enable BeaconAccessVoter for #[BeaconAccess] attribute checks')
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('assets')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('css_framework')
                            ->defaultValue('custom')
                            ->info('Which CSS framework to use: custom, bootstrap, or tailwind')
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
        // @formatter:on

        return $treeBuilder;
    }
}
