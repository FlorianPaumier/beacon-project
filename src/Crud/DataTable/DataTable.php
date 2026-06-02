<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Crud\DataTable;

use Devgeek\BeaconAdmin\Crud\DataTable\Column\Column;

class DataTable
{
    /** @var array<Column> */
    protected array $columns = [];
    protected bool $sortable = true;
    protected bool $searchable = true;
    protected int $pageSize = 25;

    public static function make(): self
    {
        return new self();
    }

    /**
     * @param array<Column> $columns
     */
    public function columns(array $columns): static
    {
        $this->columns = $columns;

        return $this;
    }

    /**
     * @return array<Column>
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    public function addColumn(Column $column): static
    {
        $this->columns[] = $column;

        return $this;
    }

    public function sortable(bool $sortable = true): static
    {
        $this->sortable = $sortable;

        return $this;
    }

    public function isSortable(): bool
    {
        return $this->sortable;
    }

    public function searchable(bool $searchable = true): static
    {
        $this->searchable = $searchable;

        return $this;
    }

    public function isSearchable(): bool
    {
        return $this->searchable;
    }

    public function pageSize(int $pageSize): static
    {
        $this->pageSize = $pageSize;

        return $this;
    }

    public function getPageSize(): int
    {
        return $this->pageSize;
    }
}
