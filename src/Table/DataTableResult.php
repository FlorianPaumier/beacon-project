<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Table;

class DataTableResult
{
    /** @param array<object> $rows */
    public function __construct(
        protected readonly array $rows,
        protected readonly int $total,
        protected readonly int $page,
        protected readonly int $perPage,
    ) {
    }

    /** @return array<object> */
    public function getRows(): array
    {
        return $this->rows;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getPerPage(): int
    {
        return $this->perPage;
    }

    public function getTotalPages(): int
    {
        if ($this->perPage === 0) {
            return 0;
        }

        return (int) ceil($this->total / $this->perPage);
    }
}
