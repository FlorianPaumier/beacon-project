<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Table;

use Devgeek\BeaconAdmin\Table\DoctrineTableAdapter;
use Devgeek\BeaconAdmin\Table\Filter\Filter;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class DoctrineTableAdapterTest extends TestCase
{
    #[Test]
    public function itAppliesFilters(): void
    {
        $filter = $this->createMock(Filter::class);
        $filter->method('getName')->willReturn('status');

        $qb = $this->createMock(QueryBuilder::class);
        $adapter = $this->createAdapter();

        $filter->expects($this->once())->method('apply')->with($qb, 'active');

        $adapter->applyFilters($qb, [$filter], ['status' => 'active']);
    }

    #[Test]
    public function itSkipsFilterWhenValueNotPresent(): void
    {
        $filter = $this->createMock(Filter::class);
        $filter->method('getName')->willReturn('status');

        $qb = $this->createMock(QueryBuilder::class);
        $adapter = $this->createAdapter();

        $filter->expects($this->never())->method('apply');

        $adapter->applyFilters($qb, [$filter], []);
    }

    #[Test]
    public function itAppliesSortAsc(): void
    {
        $qb = $this->createMock(QueryBuilder::class);
        $adapter = $this->createAdapter();

        $qb->expects($this->once())->method('orderBy')->with('o.name', 'ASC');

        $adapter->applySort($qb, 'name', 'asc');
    }

    #[Test]
    public function itAppliesSortDesc(): void
    {
        $qb = $this->createMock(QueryBuilder::class);
        $adapter = $this->createAdapter();

        $qb->expects($this->once())->method('orderBy')->with('o.name', 'DESC');

        $adapter->applySort($qb, 'name', 'desc');
    }

    #[Test]
    public function itAppliesSortDescForCapitalizedInput(): void
    {
        $qb = $this->createMock(QueryBuilder::class);
        $adapter = $this->createAdapter();

        $qb->expects($this->once())->method('orderBy')->with('o.name', 'DESC');

        $adapter->applySort($qb, 'name', 'DESC');
    }

    #[Test]
    public function itAppliesSortAscForUnknownDirection(): void
    {
        $qb = $this->createMock(QueryBuilder::class);
        $adapter = $this->createAdapter();

        $qb->expects($this->once())->method('orderBy')->with('o.name', 'ASC');

        $adapter->applySort($qb, 'name', 'invalid');
    }

    private function createAdapter(): DoctrineTableAdapter
    {
        return new DoctrineTableAdapter(
            $this->createMock(EntityManagerInterface::class),
        );
    }
}
