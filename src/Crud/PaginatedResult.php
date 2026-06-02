<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Crud;

final readonly class PaginatedResult
{
    /**
     * @param list<object> $items
     */
    public function __construct(
        public array $items,
        public int $currentPage,
        public int $itemsPerPage,
        public int $totalItems,
        public int $totalPages,
        public int $currentPageItems,
        public int $startItem,
        public int $endItem,
        public bool $hasNextPage,
        public bool $hasPreviousPage,
        public string $search = '',
        public string $sortField = '',
        public string $sortDir = 'asc',
    ) {
    }
}
