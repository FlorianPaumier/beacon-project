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

        $this->assertSame($entity, $event->getEntity());
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
        $entity = new \stdClass();
        $config = CrudConfig::make();
        $event = new BeforeCreateEvent($entity, $config);

        $this->assertSame($entity, $event->getEntity());
        $this->assertSame($config, $event->getConfig());
    }

    #[Test]
    public function itCreatesAfterCreateEvent(): void
    {
        $entity = new \stdClass();
        $config = CrudConfig::make();
        $event = new AfterCreateEvent($entity, $config);

        $this->assertSame($entity, $event->getEntity());
        $this->assertSame($config, $event->getConfig());
    }

    #[Test]
    public function itCreatesBeforeUpdateEvent(): void
    {
        $entity = new \stdClass();
        $config = CrudConfig::make();
        $event = new BeforeUpdateEvent($entity, $config);

        $this->assertSame($entity, $event->getEntity());
        $this->assertSame($config, $event->getConfig());
    }

    #[Test]
    public function itCreatesAfterUpdateEvent(): void
    {
        $entity = new \stdClass();
        $config = CrudConfig::make();
        $event = new AfterUpdateEvent($entity, $config);

        $this->assertSame($entity, $event->getEntity());
        $this->assertSame($config, $event->getConfig());
    }

    #[Test]
    public function itCreatesBeforeDeleteEvent(): void
    {
        $entity = new \stdClass();
        $config = CrudConfig::make();
        $event = new BeforeDeleteEvent($entity, $config);

        $this->assertSame($entity, $event->getEntity());
        $this->assertSame($config, $event->getConfig());
    }

    #[Test]
    public function itCreatesAfterDeleteEvent(): void
    {
        $entity = new \stdClass();
        $config = CrudConfig::make();
        $event = new AfterDeleteEvent($entity, $config);

        $this->assertSame($entity, $event->getEntity());
        $this->assertSame($config, $event->getConfig());
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
