<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Crud\DataTable;

class DataTableResult
{
    /** @param array<int, object> $results */
    public function __construct(
        protected array $results,
        protected int $total,
        protected int $page,
        protected int $perPage,
        protected int $totalPages,
        protected string $sortField,
        protected string $sortDir,
        protected string $search,
    ) {
    }

    /** @return array<int, object> */
    public function getResults(): array
    {
        return $this->results;
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
        return $this->totalPages;
    }

    public function getSortField(): string
    {
        return $this->sortField;
    }

    public function getSortDir(): string
    {
        return $this->sortDir;
    }

    public function getSearch(): string
    {
        return $this->search;
    }

    public function hasPrevious(): bool
    {
        return $this->page > 1;
    }

    public function hasNext(): bool
    {
        return $this->page < $this->totalPages;
    }
}
