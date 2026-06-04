<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routes): void {
    $routes->import('@BeaconAdminBundle/src/Controller/', 'attribute');
    $routes->import(Devgeek\BeaconAdmin\Tests\Functional\Crud\ShowPageTestCrudController::class, 'attribute');
    $routes->import(Devgeek\BeaconAdmin\Tests\Fixtures\TestApp\Controller\ClonePostCrudController::class, 'attribute');
    $routes->import(Devgeek\BeaconAdmin\Tests\Fixtures\TestApp\Controller\FilterTestPostCrudController::class, 'attribute');
};
