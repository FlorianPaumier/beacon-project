<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Event;

use Devgeek\BeaconAdmin\Event\DashboardBuiltEvent;
use Devgeek\BeaconAdmin\Widget\DashboardWidgetInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class DashboardBuiltEventTest extends TestCase
{
    private function createWidget(string $name): DashboardWidgetInterface
    {
        return new readonly class($name) implements DashboardWidgetInterface {
            public function __construct(private string $name)
            {
            }

            public function getName(): string
            {
                return $this->name;
            }

            public function getLabel(): string
            {
                return $this->name;
            }

            public function getCols(): int
            {
                return 6;
            }

            public function getPriority(): int
            {
                return 0;
            }

            public function render(): string
            {
                return '';
            }
        };
    }

    #[Test]
    public function itCreatesViaMake(): void
    {
        $event = DashboardBuiltEvent::make();

        $this->assertSame([], $event->getWidgets());
    }

    #[Test]
    public function itAcceptsWidgetsViaConstructor(): void
    {
        $widget = $this->createWidget('stats');
        $event = DashboardBuiltEvent::make([$widget]);

        $this->assertCount(1, $event->getWidgets());
    }

    #[Test]
    public function itAcceptsWidgetsViaFluentSetter(): void
    {
        $widget = $this->createWidget('stats');
        $event = DashboardBuiltEvent::make()->widgets([$widget]);

        $this->assertCount(1, $event->getWidgets());
    }

    #[Test]
    public function itAddsWidgets(): void
    {
        $event = DashboardBuiltEvent::make();
        $event->addWidget($this->createWidget('stats'));

        $this->assertCount(1, $event->getWidgets());
    }

    #[Test]
    public function itRemovesWidgetsByName(): void
    {
        $event = DashboardBuiltEvent::make();
        $event->addWidget($this->createWidget('stats'));
        $event->addWidget($this->createWidget('chart'));
        $event->removeWidget('stats');

        $this->assertCount(1, $event->getWidgets());
        $this->assertSame('chart', $event->getWidgets()[0]->getName());
    }

    #[Test]
    public function itDoesNothingWhenRemovingUnknownWidget(): void
    {
        $event = DashboardBuiltEvent::make();
        $event->addWidget($this->createWidget('stats'));
        $event->removeWidget('nonexistent');

        $this->assertCount(1, $event->getWidgets());
    }

    #[Test]
    public function itSupportsFluentInterface(): void
    {
        $event = DashboardBuiltEvent::make();
        $result = $event->addWidget($this->createWidget('stats'));

        $this->assertSame($event, $result);
    }
}
