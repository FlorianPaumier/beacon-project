<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\DependencyInjection\Compiler;

use Devgeek\BeaconAdmin\DependencyInjection\Compiler\WidgetPass;
use Devgeek\BeaconAdmin\Widget\WidgetRegistry;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

final class WidgetPassTest extends TestCase
{
    #[Test]
    public function itRegistersTaggedWidgetsOnRegistry(): void
    {
        $container = new ContainerBuilder();
        $registryDef = new Definition(WidgetRegistry::class);
        $container->setDefinition(WidgetRegistry::class, $registryDef);

        $widgetDef = new Definition(\stdClass::class);
        $widgetDef->addTag('beacon_admin.widget');
        $container->setDefinition('widget_1', $widgetDef);

        $pass = new WidgetPass();
        $pass->process($container);

        $methodCalls = $registryDef->getMethodCalls();

        $this->assertCount(1, $methodCalls);
        $this->assertSame('register', $methodCalls[0][0]);
        $this->assertInstanceOf(Reference::class, $methodCalls[0][1][0]);
    }

    #[Test]
    public function itSkipsWhenRegistryIsNotDefined(): void
    {
        $container = new ContainerBuilder();

        $pass = new WidgetPass();
        $pass->process($container);

        $this->assertFalse($container->has(WidgetRegistry::class));
    }

    #[Test]
    public function itRegistersMultipleWidgets(): void
    {
        $container = new ContainerBuilder();
        $registryDef = new Definition(WidgetRegistry::class);
        $container->setDefinition(WidgetRegistry::class, $registryDef);

        $def1 = new Definition(\stdClass::class);
        $def1->addTag('beacon_admin.widget');
        $container->setDefinition('w_1', $def1);

        $def2 = new Definition(\stdClass::class);
        $def2->addTag('beacon_admin.widget');
        $container->setDefinition('w_2', $def2);

        $pass = new WidgetPass();
        $pass->process($container);

        $this->assertCount(2, $registryDef->getMethodCalls());
    }
}
