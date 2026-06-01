<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Widget;

use Devgeek\BeaconAdmin\Widget\DashboardWidgetInterface;
use Devgeek\BeaconAdmin\Widget\WidgetRegistry;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class WidgetRegistryTest extends TestCase
{
    #[Test]
    public function itIsEmptyByDefault(): void
    {
        $registry = WidgetRegistry::make();

        $this->assertSame([], $registry->all());
    }

    #[Test]
    public function itRegistersWidgets(): void
    {
        $widget = $this->createWidget('stats', 'Statistics');
        $registry = WidgetRegistry::make();
        $registry->register($widget);

        $this->assertCount(1, $registry->all());
    }

    #[Test]
    public function itRetrievesWidgetByName(): void
    {
        $widget = $this->createWidget('stats', 'Statistics');
        $registry = WidgetRegistry::make();
        $registry->register($widget);

        $this->assertSame($widget, $registry->get('stats'));
    }

    #[Test]
    public function itReturnsNullForUnknownWidget(): void
    {
        $registry = WidgetRegistry::make();

        $this->assertNull($registry->get('nonexistent'));
    }

    #[Test]
    public function itOverwritesWidgetWithSameName(): void
    {
        $first = $this->createWidget('stats', 'First');
        $second = $this->createWidget('stats', 'Second');
        $registry = WidgetRegistry::make();
        $registry->register($first);
        $registry->register($second);

        $this->assertCount(1, $registry->all());
        $this->assertSame('Second', $registry->get('stats')->getLabel());
    }

    #[Test]
    public function itSortsWidgetsByPriority(): void
    {
        $high = $this->createWidget('a', 'High', 0);
        $low = $this->createWidget('b', 'Low', 10);
        $registry = WidgetRegistry::make();
        $registry->register($low);
        $registry->register($high);

        $all = $registry->all();
        $this->assertSame('High', $all[0]->getLabel());
        $this->assertSame('Low', $all[1]->getLabel());
    }

    #[Test]
    public function itSupportsFluentInterface(): void
    {
        $widget = $this->createWidget('stats', 'Stats');
        $registry = WidgetRegistry::make();
        $result = $registry->register($widget);

        $this->assertSame($registry, $result);
    }

    private function createWidget(string $name, string $label, int $priority = 0): DashboardWidgetInterface
    {
        return new readonly class($name, $label, $priority) implements DashboardWidgetInterface {
            public function __construct(
                private string $name,
                private string $label,
                private int $priority = 0,
            ) {
            }

            public function getName(): string
            {
                return $this->name;
            }

            public function getLabel(): string
            {
                return $this->label;
            }

            public function getCols(): int
            {
                return 6;
            }

            public function getPriority(): int
            {
                return $this->priority;
            }

            public function render(): string
            {
                return '';
            }
        };
    }
}
