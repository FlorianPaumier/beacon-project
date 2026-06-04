<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Crud\DataTable\Column;

class ColumnGroup
{
    protected string $label;

    /** @var array<string> */
    protected array $columns = [];

    public static function make(string $label): self
    {
        $group = new self();
        $group->label = $label;

        return $group;
    }

    /** @param array<string> $columns */
    public function columns(array $columns): static
    {
        $this->columns = $columns;

        return $this;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    /** @return array<string> */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * Given all columns in the table, return the subset that belongs to this group,
     * preserving the original order.
     *
     * @param array<Column> $allColumns
     *
     * @return array<Column>
     */
    public function filterColumns(array $allColumns): array
    {
        $result = [];

        foreach ($allColumns as $col) {
            if (\in_array($col->getName(), $this->columns, true)) {
                $result[] = $col;
            }
        }

        return $result;
    }

    /** @param array<Column> $allColumns */
    public function getColspan(array $allColumns): int
    {
        return \count($this->filterColumns($allColumns));
    }
}
