<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin;

use Devgeek\BeaconAdmin\Controller\NotificationController;
use Devgeek\BeaconAdmin\DependencyInjection\Compiler\MenuPass;
use Devgeek\BeaconAdmin\DependencyInjection\Compiler\WidgetPass;
use Devgeek\BeaconAdmin\EventSubscriber\LoginRedirectSubscriber;
use Devgeek\BeaconAdmin\Security\BeaconAccessVoter;
use Devgeek\BeaconAdmin\Security\LoginFormAuthenticator;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class BeaconAdminBundle extends AbstractBundle
{
    /**
     * Auto-inject the admin firewall and access control into the app's
     * security config — the user doesn't need to write it manually.
     *
     * security.firewalls uses performNoDeepMerging() which prevents adding
     * firewalls across config sources. We work around this by merging the
     * admin firewall into the app's first security config set via reflection
     * on ContainerBuilder. This is safe because we only run once and only if
     * no admin firewall already exists.
     */
    public function prependExtension(ContainerConfigurator $configurator, ContainerBuilder $container): void
    {
        $configs = $container->getExtensionConfig('security');

        if ([] === $configs) {
            return;
        }

        // Only prepend if no admin firewall is already defined
        foreach ($configs as $config) {
            if (isset($config['firewalls']['admin'])) {
                return;
            }
        }

        // Skip if any firewall uses http_basic/stateless (e.g. test environments)
        foreach ($configs as $config) {
            foreach ($config['firewalls'] ?? [] as $fw) {
                if (array_key_exists('http_basic', $fw) || ($fw['stateless'] ?? false)) {
                    return;
                }
            }
        }

        // Auto-detect the user provider from the first non-dev firewall
        $provider = null;
        foreach ($configs as $config) {
            foreach ($config['firewalls'] ?? [] as $name => $fw) {
                if ('dev' === $name) {
                    continue;
                }
                if (isset($fw['provider'])) {
                    $provider = $fw['provider'];
                    break 2;
                }
            }
        }

        $adminFirewall = [
            'lazy' => true,
            'pattern' => '^/([a-z]{2}/)?admin',
            'provider' => $provider,
            'form_login' => [
                'login_path' => 'beacon_admin_login',
                'check_path' => 'beacon_admin_login',
                'default_target_path' => 'beacon_admin.dashboard_locale',
            ],
            'logout' => [
                'path' => 'beacon_admin_logout',
                'target' => 'beacon_admin_login',
            ],
        ];

        $adminAccessControl = [
            ['path' => '^/([a-z]{2}/)?admin/login', 'roles' => 'PUBLIC_ACCESS'],
            ['path' => '^/([a-z]{2}/)?admin', 'roles' => 'ROLE_ADMIN'],
        ];

        // Merge admin firewall before existing firewalls in the first config set
        $configs[0]['firewalls'] = isset($configs[0]['firewalls'])
            ? ['admin' => $adminFirewall] + $configs[0]['firewalls']
            : ['admin' => $adminFirewall];

        // Prepend admin access control rules
        $configs[0]['access_control'] = array_merge(
            $adminAccessControl,
            $configs[0]['access_control'] ?? [],
        );

        // Replace only the security extension configs (preserve all others)
        $refl = new \ReflectionClass($container);
        $prop = $refl->getProperty('extensionConfigs');
        $allConfigs = $prop->getValue($container);
        $allConfigs['security'] = $configs;
        $prop->setValue($container, $allConfigs);
    }

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
                        'modern' => 'bundles/beaconadmin/dist/beacon-modern.css',
                        'enterprise' => 'bundles/beaconadmin/dist/beacon-enterprise.css',
                        'brut' => 'bundles/beaconadmin/dist/beacon-brut.css',
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
                        ->booleanNode('redirect_to_login')
                            ->defaultTrue()
                            ->info('Redirect unauthenticated users to the login page')
                        ->end()
                        ->booleanNode('use_builtin_authenticator')
                            ->defaultFalse()
                            ->info('Wire LoginFormAuthenticator into the container (set true then reference Devgeek\BeaconAdmin\Security\LoginFormAuthenticator in security.yaml custom_authenticator)')
                        ->end()
                        ->scalarNode('login_route')
                            ->defaultValue('beacon_admin_login')
                            ->info('Route name for the login page')
                        ->end()
                        ->arrayNode('locales')
                            ->scalarPrototype()->end()
                            ->defaultValue([])
                            ->info('Supported locale codes for login redirect (empty = auto-detect from Accept-Language)')
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('brand')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('name')->defaultValue('Beacon Admin')->end()
                        ->scalarNode('logo_path')->defaultNull()->end()
                        ->scalarNode('favicon_path')->defaultNull()->end()
                        ->scalarNode('primary_color')->defaultValue('#2563eb')->end()
                        ->scalarNode('accent_color')->defaultValue('#0ea5e9')->end()
                        ->scalarNode('support_email')->defaultNull()->end()
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

        if ((bool) $config['security']['redirect_to_login']) {
            $builder->findDefinition(LoginRedirectSubscriber::class)
                ->setArgument('$enabled', true)
                ->setArgument('$loginRoute', $config['security']['login_route'])
                ->setArgument('$routePrefix', $config['route_prefix'])
                ->setArgument('$locales', $config['security']['locales']);
        } else {
            $builder->removeDefinition(LoginRedirectSubscriber::class);
        }

        if ((bool) $config['security']['use_builtin_authenticator']) {
            $builder->findDefinition(LoginFormAuthenticator::class)
                ->setArgument('$loginRoute', $config['security']['login_route'])
                ->setArgument('$afterLoginRedirect', 'beacon_admin_dashboard')
                ->setArgument('$firewallName', 'admin');
        } else {
            $builder->removeDefinition(LoginFormAuthenticator::class);
        }

        $builder->findDefinition(NotificationController::class)
            ->setArgument('$adminRole', $config['security']['role']);

        $builder->setParameter('beacon_admin.brand.name', $config['brand']['name']);
        $builder->setParameter('beacon_admin.brand.logo_path', $config['brand']['logo_path']);
        $builder->setParameter('beacon_admin.brand.favicon_path', $config['brand']['favicon_path']);
        $builder->setParameter('beacon_admin.brand.primary_color', $config['brand']['primary_color']);
        $builder->setParameter('beacon_admin.brand.accent_color', $config['brand']['accent_color']);
        $builder->setParameter('beacon_admin.brand.support_email', $config['brand']['support_email']);
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
