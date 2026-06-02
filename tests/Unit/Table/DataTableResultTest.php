<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Table;

use Devgeek\BeaconAdmin\Table\DataTableResult;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class DataTableResultTest extends TestCase
{
    #[Test]
    public function itReturnsRows(): void
    {
        $rows = [(object) ['id' => 1], (object) ['id' => 2]];
        $result = new DataTableResult($rows, 2, 1, 10);

        $this->assertSame($rows, $result->getRows());
    }

    #[Test]
    public function itReturnsTotal(): void
    {
        $result = new DataTableResult([], 100, 1, 10);

        $this->assertSame(100, $result->getTotal());
    }

    #[Test]
    public function itReturnsPage(): void
    {
        $result = new DataTableResult([], 0, 3, 10);

        $this->assertSame(3, $result->getPage());
    }

    #[Test]
    public function itReturnsPerPage(): void
    {
        $result = new DataTableResult([], 0, 1, 25);

        $this->assertSame(25, $result->getPerPage());
    }

    #[Test]
    public function itCalculatesTotalPages(): void
    {
        $result = new DataTableResult([], 50, 1, 10);

        $this->assertSame(5, $result->getTotalPages());
    }

    #[Test]
    public function itRoundsUpTotalPages(): void
    {
        $result = new DataTableResult([], 51, 1, 10);

        $this->assertSame(6, $result->getTotalPages());
    }

    #[Test]
    public function itReturnsZeroTotalPagesWhenPerPageIsZero(): void
    {
        $result = new DataTableResult([], 50, 1, 0);

        $this->assertSame(0, $result->getTotalPages());
    }

    #[Test]
    public function itReturnsOneTotalPageForEmptyResult(): void
    {
        $result = new DataTableResult([], 0, 1, 10);

        $this->assertSame(0, $result->getTotalPages());
    }

    #[Test]
    public function itHandlesPartialLastPage(): void
    {
        $result = new DataTableResult([], 11, 1, 10);

        $this->assertSame(2, $result->getTotalPages());
    }
}
