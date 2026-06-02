<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Crud\DataTable;

use Devgeek\BeaconAdmin\Crud\DataTable\DataTableResult;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class DataTableResultTest extends TestCase
{
    #[Test]
    public function itReturnsResults(): void
    {
        $result = $this->createResult([], 0, 1, 25, 0, 'id', 'asc', '');

        $this->assertSame([], $result->getResults());
    }

    #[Test]
    public function itReturnsTotal(): void
    {
        $result = $this->createResult([], 100, 1, 25, 4, 'id', 'asc', '');

        $this->assertSame(100, $result->getTotal());
    }

    #[Test]
    public function itReturnsPage(): void
    {
        $result = $this->createResult([], 100, 2, 25, 4, 'id', 'asc', '');

        $this->assertSame(2, $result->getPage());
    }

    #[Test]
    public function itReturnsPerPage(): void
    {
        $result = $this->createResult([], 100, 1, 10, 10, 'id', 'asc', '');

        $this->assertSame(10, $result->getPerPage());
    }

    #[Test]
    public function itReturnsTotalPages(): void
    {
        $result = $this->createResult([], 100, 1, 25, 4, 'id', 'asc', '');

        $this->assertSame(4, $result->getTotalPages());
    }

    #[Test]
    public function itReturnsSortField(): void
    {
        $result = $this->createResult([], 0, 1, 25, 0, 'name', 'asc', '');

        $this->assertSame('name', $result->getSortField());
    }

    #[Test]
    public function itReturnsSortDir(): void
    {
        $result = $this->createResult([], 0, 1, 25, 0, 'id', 'desc', '');

        $this->assertSame('desc', $result->getSortDir());
    }

    #[Test]
    public function itReturnsSearch(): void
    {
        $result = $this->createResult([], 0, 1, 25, 0, 'id', 'asc', 'keyword');

        $this->assertSame('keyword', $result->getSearch());
    }

    #[Test]
    public function itHasNoPreviousOnFirstPage(): void
    {
        $result = $this->createResult([], 100, 1, 25, 4, 'id', 'asc', '');

        $this->assertFalse($result->hasPrevious());
    }

    #[Test]
    public function itHasPreviousOnSecondPage(): void
    {
        $result = $this->createResult([], 100, 2, 25, 4, 'id', 'asc', '');

        $this->assertTrue($result->hasPrevious());
    }

    #[Test]
    public function itHasNextWhenNotOnLastPage(): void
    {
        $result = $this->createResult([], 100, 1, 25, 4, 'id', 'asc', '');

        $this->assertTrue($result->hasNext());
    }

    #[Test]
    public function itHasNoNextOnLastPage(): void
    {
        $result = $this->createResult([], 100, 4, 25, 4, 'id', 'asc', '');

        $this->assertFalse($result->hasNext());
    }

    #[Test]
    public function itHasNoNextWhenOnlyOnePage(): void
    {
        $result = $this->createResult([], 5, 1, 25, 1, 'id', 'asc', '');

        $this->assertFalse($result->hasNext());
    }

    /** @param array<int, object> $results */
    private function createResult(
        array $results,
        int $total,
        int $page,
        int $perPage,
        int $totalPages,
        string $sortField,
        string $sortDir,
        string $search,
    ): DataTableResult {
        return new DataTableResult(
            $results,
            $total,
            $page,
            $perPage,
            $totalPages,
            $sortField,
            $sortDir,
            $search,
        );
    }
}
