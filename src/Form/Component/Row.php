<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Form\Component;

class Row
{
    /** @var array<Column> */
    private array $columns = [];

    public static function make(): self
    {
        return new self();
    }

    /** @param array<Column> $columns */
    public function columns(array $columns): self
    {
        $this->columns = $columns;

        return $this;
    }

    public function add(Column $column): self
    {
        $this->columns[] = $column;

        return $this;
    }

    /** @return array<Column> */
    public function getColumns(): array
    {
        return $this->columns;
    }
}
