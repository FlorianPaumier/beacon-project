<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Crud;

use Devgeek\BeaconAdmin\Crud\PaginatedResult;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class PaginatedResultTest extends TestCase
{
    #[Test]
    public function itCreatesWithAllProperties(): void
    {
        $result = new PaginatedResult(
            items: ['a', 'b'],
            currentPage: 2,
            itemsPerPage: 10,
            totalItems: 45,
            totalPages: 5,
            currentPageItems: 10,
            startItem: 11,
            endItem: 20,
            hasNextPage: true,
            hasPreviousPage: true,
        );

        $this->assertSame(['a', 'b'], $result->items);
        $this->assertSame(2, $result->currentPage);
        $this->assertSame(10, $result->itemsPerPage);
        $this->assertSame(45, $result->totalItems);
        $this->assertSame(5, $result->totalPages);
        $this->assertSame(10, $result->currentPageItems);
        $this->assertSame(11, $result->startItem);
        $this->assertSame(20, $result->endItem);
        $this->assertTrue($result->hasNextPage);
        $this->assertTrue($result->hasPreviousPage);
    }

    #[Test]
    public function itHandlesFirstPage(): void
    {
        $result = new PaginatedResult(
            items: [],
            currentPage: 1,
            itemsPerPage: 25,
            totalItems: 0,
            totalPages: 1,
            currentPageItems: 0,
            startItem: 0,
            endItem: 0,
            hasNextPage: false,
            hasPreviousPage: false,
        );

        $this->assertFalse($result->hasPreviousPage);
        $this->assertFalse($result->hasNextPage);
        $this->assertSame(0, $result->startItem);
    }

    #[Test]
    public function itIsReadonly(): void
    {
        $result = new PaginatedResult(
            items: [],
            currentPage: 1,
            itemsPerPage: 25,
            totalItems: 0,
            totalPages: 1,
            currentPageItems: 0,
            startItem: 0,
            endItem: 0,
            hasNextPage: false,
            hasPreviousPage: false,
        );

        $refl = new \ReflectionClass($result);
        $this->assertTrue($refl->isReadOnly());
    }
}
