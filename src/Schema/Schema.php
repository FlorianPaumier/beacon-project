<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Schema;

use Devgeek\BeaconAdmin\Support\Component;

class Schema
{
    /** @var array<Component> */
    protected array $components = [];

    /** @var array<string, mixed> */
    protected array $state = [];

    /** @param array<Component> $components */
    public function schema(array $components): static
    {
        $this->components = $components;

        return $this;
    }

    /** @return array<Component> */
    public function getComponents(): array
    {
        return $this->components;
    }

    /** @return array<string, mixed> */
    public function getState(): array
    {
        return $this->state;
    }

    /** @param array<string, mixed> $state */
    public function fill(array $state): static
    {
        $this->state = $state;

        return $this;
    }
}
