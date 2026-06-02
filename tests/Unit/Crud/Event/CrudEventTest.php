<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Crud\Event;

use Devgeek\BeaconAdmin\Crud\CrudConfig;
use Devgeek\BeaconAdmin\Crud\Event\AfterCreateEvent;
use Devgeek\BeaconAdmin\Crud\Event\AfterDeleteEvent;
use Devgeek\BeaconAdmin\Crud\Event\AfterUpdateEvent;
use Devgeek\BeaconAdmin\Crud\Event\BeforeCreateEvent;
use Devgeek\BeaconAdmin\Crud\Event\BeforeDeleteEvent;
use Devgeek\BeaconAdmin\Crud\Event\BeforeUpdateEvent;
use Devgeek\BeaconAdmin\Crud\Event\CrudEvent;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class CrudEventTest extends TestCase
{
    #[Test]
    public function itCreatesCrudEvent(): void
    {
        $entity = new \stdClass();
        $config = CrudConfig::make();
        $event = new CrudEvent($entity, $config);

        $this->assertInstanceOf(CrudEvent::class, $event);
    }

    #[Test]
    public function itReturnsEntity(): void
    {
        $entity = new \stdClass();
        $config = CrudConfig::make();
        $event = new CrudEvent($entity, $config);

        $this->assertSame($entity, $event->getEntity());
    }

    #[Test]
    public function itReturnsConfig(): void
    {
        $entity = new \stdClass();
        $config = CrudConfig::make();
        $event = new CrudEvent($entity, $config);

        $this->assertSame($config, $event->getConfig());
    }

    #[Test]
    public function itCreatesBeforeCreateEvent(): void
    {
        $event = new BeforeCreateEvent(new \stdClass(), CrudConfig::make());

        $this->assertInstanceOf(CrudEvent::class, $event);
        $this->assertInstanceOf(BeforeCreateEvent::class, $event);
    }

    #[Test]
    public function itCreatesAfterCreateEvent(): void
    {
        $event = new AfterCreateEvent(new \stdClass(), CrudConfig::make());

        $this->assertInstanceOf(CrudEvent::class, $event);
        $this->assertInstanceOf(AfterCreateEvent::class, $event);
    }

    #[Test]
    public function itCreatesBeforeUpdateEvent(): void
    {
        $event = new BeforeUpdateEvent(new \stdClass(), CrudConfig::make());

        $this->assertInstanceOf(CrudEvent::class, $event);
        $this->assertInstanceOf(BeforeUpdateEvent::class, $event);
    }

    #[Test]
    public function itCreatesAfterUpdateEvent(): void
    {
        $event = new AfterUpdateEvent(new \stdClass(), CrudConfig::make());

        $this->assertInstanceOf(CrudEvent::class, $event);
        $this->assertInstanceOf(AfterUpdateEvent::class, $event);
    }

    #[Test]
    public function itCreatesBeforeDeleteEvent(): void
    {
        $event = new BeforeDeleteEvent(new \stdClass(), CrudConfig::make());

        $this->assertInstanceOf(CrudEvent::class, $event);
        $this->assertInstanceOf(BeforeDeleteEvent::class, $event);
    }

    #[Test]
    public function itCreatesAfterDeleteEvent(): void
    {
        $event = new AfterDeleteEvent(new \stdClass(), CrudConfig::make());

        $this->assertInstanceOf(CrudEvent::class, $event);
        $this->assertInstanceOf(AfterDeleteEvent::class, $event);
    }

    #[Test]
    public function itReturnsEntityFromSubclass(): void
    {
        $entity = new \stdClass();
        $config = CrudConfig::make();
        $event = new BeforeCreateEvent($entity, $config);

        $this->assertSame($entity, $event->getEntity());
        $this->assertSame($config, $event->getConfig());
    }
}
