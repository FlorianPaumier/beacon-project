<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Crud\DataTable;

use Devgeek\BeaconAdmin\Crud\DataTable\Column\Column;
use Devgeek\BeaconAdmin\Crud\DataTable\Column\ColumnGroup;

class DataTable
{
    /** @var array<Column> */
    protected array $columns = [];
    /** @var array<ColumnGroup> */
    protected array $groups = [];
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

    /** @param array<ColumnGroup> $groups */
    public function groups(array $groups): static
    {
        $this->groups = $groups;

        return $this;
    }

    /** @return array<ColumnGroup> */
    public function getGroups(): array
    {
        return $this->groups;
    }

    /**
     * Returns all column names that belong to at least one group.
     *
     * @return array<string>
     */
    public function getGroupedColumnNames(): array
    {
        $names = [];

        foreach ($this->groups as $group) {
            foreach ($group->getColumns() as $colName) {
                $names[] = $colName;
            }
        }

        return array_unique($names);
    }

    /**
     * Returns columns that are NOT in any group.
     *
     * @return array<Column>
     */
    public function getUngroupedColumns(): array
    {
        $grouped = $this->getGroupedColumnNames();

        return array_filter(
            $this->columns,
            static fn (Column $col) => !\in_array($col->getName(), $grouped, true),
        );
    }
}
