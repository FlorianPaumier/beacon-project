<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\DependencyInjection\Compiler;

use Devgeek\BeaconAdmin\Widget\WidgetRegistry;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class WidgetPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (! $container->has(WidgetRegistry::class)) {
            return;
        }

        $registry = $container->findDefinition(WidgetRegistry::class);

        foreach ($container->findTaggedServiceIds('beacon_admin.widget') as $id => $tags) {
            $registry->addMethodCall('register', [new Reference($id)]);
        }
    }
}
