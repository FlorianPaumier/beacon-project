<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Form\Component;

class Column
{
    private int $span = 12;
    /** @var array<object> */
    private array $components = [];

    public static function make(int $span = 6): self
    {
        $column = new self();
        $column->span = $span;

        return $column;
    }

    public function span(int $span): self
    {
        $this->span = $span;

        return $this;
    }

    public function getSpan(): int
    {
        return $this->span;
    }

    /** @param array<object> $components */
    public function components(array $components): self
    {
        $this->components = $components;

        return $this;
    }

    /** @return array<object> */
    public function getComponents(): array
    {
        return $this->components;
    }
}
