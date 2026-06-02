<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\DependencyInjection\Compiler;

use Devgeek\BeaconAdmin\DependencyInjection\Compiler\MenuPass;
use Devgeek\BeaconAdmin\Menu\MenuBuilder;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

final class MenuPassTest extends TestCase
{
    #[Test]
    public function itRegistersTaggedExtensionsOnMenuBuilder(): void
    {
        $container = new ContainerBuilder();
        $menuBuilderDef = new Definition(MenuBuilder::class);
        $container->setDefinition(MenuBuilder::class, $menuBuilderDef);

        $extensionDef = new Definition(\stdClass::class);
        $extensionDef->addTag('beacon_admin.menu_extension');
        $container->setDefinition('menu_extension_1', $extensionDef);

        $pass = new MenuPass();
        $pass->process($container);

        $methodCalls = $menuBuilderDef->getMethodCalls();

        $this->assertCount(1, $methodCalls);
        $this->assertSame('addExtension', $methodCalls[0][0]);
        $this->assertInstanceOf(Reference::class, $methodCalls[0][1][0]);
    }

    #[Test]
    public function itSkipsWhenMenuBuilderIsNotDefined(): void
    {
        $container = new ContainerBuilder();

        $pass = new MenuPass();
        $pass->process($container);

        $this->assertFalse($container->has(MenuBuilder::class));
    }

    #[Test]
    public function itRegistersMultipleExtensions(): void
    {
        $container = new ContainerBuilder();
        $menuBuilderDef = new Definition(MenuBuilder::class);
        $container->setDefinition(MenuBuilder::class, $menuBuilderDef);

        $def1 = new Definition(\stdClass::class);
        $def1->addTag('beacon_admin.menu_extension');
        $container->setDefinition('ext_1', $def1);

        $def2 = new Definition(\stdClass::class);
        $def2->addTag('beacon_admin.menu_extension');
        $container->setDefinition('ext_2', $def2);

        $pass = new MenuPass();
        $pass->process($container);

        $this->assertCount(2, $menuBuilderDef->getMethodCalls());
    }
}
