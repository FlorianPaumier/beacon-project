<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Crud\Event;

use Devgeek\BeaconAdmin\Crud\CrudConfig;

class CrudEvent
{
    public function __construct(
        protected object $entity,
        protected CrudConfig $config,
    ) {
    }

    public function getEntity(): object
    {
        return $this->entity;
    }

    public function getConfig(): CrudConfig
    {
        return $this->config;
    }
}
