<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\DependencyInjection\Compiler;

use Devgeek\BeaconAdmin\Menu\MenuBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final readonly class MenuPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (! $container->has(MenuBuilder::class)) {
            return;
        }

        $builder = $container->findDefinition(MenuBuilder::class);

        foreach ($container->findTaggedServiceIds('beacon_admin.menu_extension') as $id => $tags) {
            $builder->addMethodCall('addExtension', [new Reference($id)]);
        }
    }
}
