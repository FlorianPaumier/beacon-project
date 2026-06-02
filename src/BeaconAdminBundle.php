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
                ->arrayNode('themes')
                    ->useAttributeAsKey('name')
                    ->scalarPrototype()->end()
                    ->defaultValue([
                        'modern' => 'bundles/beaconadmin/beacon-modern.css',
                        'enterprise' => 'bundles/beaconadmin/beacon-enterprise.css',
                        'brut' => 'bundles/beaconadmin/beacon-brut.css',
                    ])
                    ->info('Map of theme name → CSS file path')
                ->end()
                ->scalarNode('default_theme')
                    ->defaultValue('modern')
                    ->info('Default theme name (must be a key in "themes")')
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
                ->arrayNode('upload')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('target_directory')
                            ->defaultValue('%kernel.project_dir%/public/uploads')
                            ->info('Directory to store uploaded files (absolute or relative to project root)')
                        ->end()
                        ->scalarNode('public_path')
                            ->defaultValue('/uploads')
                            ->info('Public URL path prefix for uploads')
                        ->end()
                        ->arrayNode('allowed_mime_types')
                            ->scalarPrototype()->end()
                            ->defaultValue(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
                            ->info('Allowed MIME types for file uploads')
                        ->end()
                        ->integerNode('max_size')
                            ->defaultValue(10485760)
                            ->info('Maximum file size in bytes (default 10MB)')
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('security')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('role')->defaultValue('ROLE_ADMIN')->end()
                        ->booleanNode('voters')
                            ->defaultTrue()
                            ->info('Enable BeaconAccessVoter for #[BeaconAccess] attribute checks')
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    /**
     * Hook: loads services and processes merged configuration.
     * Replaces the standalone Extension class.
     */
    /** @param array<mixed> $config */
    public function loadExtension(
        array $config,
        ContainerConfigurator $container,
        ContainerBuilder $builder,
    ): void {
        $loader = new PhpFileLoader($builder, new FileLocator(__DIR__.'/../config'));
        $loader->load('services.php');

        $builder->setParameter('beacon_admin.config', $config);
        $builder->setParameter('beacon_admin.route_prefix', $config['route_prefix']);
        $builder->setParameter('beacon_admin.title', $config['title']);
        $builder->setParameter('beacon_admin.themes', $config['themes']);
        $builder->setParameter('beacon_admin.default_theme', $config['default_theme']);
        $builder->setParameter('beacon_admin.menu.items', $config['menu']);
        $builder->setParameter('beacon_admin.security.role', $config['security']['role']);
        $uploadDir = $config['upload']['target_directory'];
        if (!str_starts_with($uploadDir, '/')) {
            $uploadDir = $builder->getParameter('kernel.project_dir').'/'.$uploadDir;
        }
        $builder->setParameter('beacon_admin.upload.target_directory', $uploadDir);
        $builder->setParameter('beacon_admin.upload.public_path', $config['upload']['public_path']);
        $builder->setParameter('beacon_admin.upload.allowed_mime_types', $config['upload']['allowed_mime_types']);
        $builder->setParameter('beacon_admin.upload.max_size', $config['upload']['max_size']);

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
