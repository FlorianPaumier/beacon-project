<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Devgeek\BeaconAdmin\Controller\GlobalSearchController;
use Devgeek\BeaconAdmin\Menu\MenuBuilder;
use Devgeek\BeaconAdmin\Search\GlobalSearchProviderInterface;
use Devgeek\BeaconAdmin\Twig\AdminRuntime;
use Devgeek\BeaconAdmin\Twig\BeaconAdminExtension;
use Devgeek\BeaconAdmin\Twig\BreadcrumbExtension;
use Devgeek\BeaconAdmin\Twig\BreadcrumbRenderer;
use Devgeek\BeaconAdmin\Twig\SchemaExtension;
use Devgeek\BeaconAdmin\Upload\LocalMediaUploader;
use Devgeek\BeaconAdmin\Upload\MediaUploaderInterface;
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

    // Twig runtime (lazy-loaded)
    $services->set(AdminRuntime::class)
        ->arg('$config', '%beacon_admin.config%')
        ->arg('$requestStack', service('request_stack'))
        ->tag('twig.runtime');

    // Schema Twig extension (Turbo frame integration)
    $services->set(SchemaExtension::class)->tag('twig.extension');

    // Breadcrumbs
    $services->set(BreadcrumbExtension::class)->tag('twig.extension');
    $services->set(BreadcrumbRenderer::class)
        ->tag('beacon_admin.breadcrumb_renderer')
        ->tag('twig.runtime');

    // Default media uploader (local filesystem)
    // Override MediaUploaderInterface alias to use S3, GCS, etc.
    $services->alias(MediaUploaderInterface::class, LocalMediaUploader::class);
    $services->set(LocalMediaUploader::class)
        ->arg('$targetDirectory', '%beacon_admin.upload.target_directory%')
        ->arg('$publicPath', '%beacon_admin.upload.public_path%')
        ->arg('$allowedMimeTypes', '%beacon_admin.upload.allowed_mime_types%')
        ->arg('$maxSize', '%beacon_admin.upload.max_size%');

    // Global search: auto-tag providers implementing the interface
    $services->instanceof(GlobalSearchProviderInterface::class)
        ->tag('beacon_admin.global_search_provider');

    $services->set(GlobalSearchController::class)
        ->arg('$providers', tagged_iterator('beacon_admin.global_search_provider'));
};
