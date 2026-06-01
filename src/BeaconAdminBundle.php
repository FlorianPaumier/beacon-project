<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin;

use Devgeek\BeaconAdmin\DependencyInjection\BeaconAdminExtension;
use Devgeek\BeaconAdmin\DependencyInjection\Compiler\MenuPass;
use Devgeek\BeaconAdmin\DependencyInjection\Compiler\WidgetPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class BeaconAdminBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new WidgetPass());
        $container->addCompilerPass(new MenuPass());
    }

    public function getContainerExtension(): ?ExtensionInterface
    {
        if (null === $this->extension) {
            $this->extension = new BeaconAdminExtension();
        }

        return $this->extension;
    }

}
