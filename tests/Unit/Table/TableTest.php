<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Table;

use Devgeek\BeaconAdmin\Table\Column\TextColumn;
use Devgeek\BeaconAdmin\Table\DataTableResult;
use Devgeek\BeaconAdmin\Table\DoctrineTableAdapter;
use Devgeek\BeaconAdmin\Table\Filter\Filter;
use Devgeek\BeaconAdmin\Table\Table;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

final class TableTest extends TestCase
{
    #[Test]
    public function itSetsQueryFluently(): void
    {
        $table = $this->createTable();
        $table->query('App\Entity\User');

        $this->assertSame('App\Entity\User', $table->getQuery());
    }

    #[Test]
    public function itReturnsNullQueryByDefault(): void
    {
        $table = $this->createTable();

        $this->assertNull($table->getQuery());
    }

    #[Test]
    public function itSetsColumnsFluently(): void
    {
        $column = TextColumn::make()->name('name');
        $table = $this->createTable()->columns([$column]);

        $this->assertCount(1, $table->getColumns());
        $this->assertSame($column, $table->getColumns()[0]);
    }

    #[Test]
    public function itReturnsEmptyColumnsByDefault(): void
    {
        $table = $this->createTable();

        $this->assertSame([], $table->getColumns());
    }

    #[Test]
    public function itSetsFiltersFluently(): void
    {
        $filter = $this->createMock(Filter::class);
        $table = $this->createTable()->filters([$filter]);

        $this->assertCount(1, $table->getFilters());
        $this->assertSame($filter, $table->getFilters()[0]);
    }

    #[Test]
    public function itReturnsEmptyFiltersByDefault(): void
    {
        $table = $this->createTable();

        $this->assertSame([], $table->getFilters());
    }

    #[Test]
    public function itSetsDefaultSortFluently(): void
    {
        $table = $this->createTable()->defaultSort('name', 'desc');

        $this->assertSame('name', $table->getDefaultSortField());
        $this->assertSame('desc', $table->getDefaultSortDirection());
    }

    #[Test]
    public function itHasDefaultSortDefaults(): void
    {
        $table = $this->createTable();

        $this->assertSame('id', $table->getDefaultSortField());
        $this->assertSame('asc', $table->getDefaultSortDirection());
    }

    #[Test]
    public function itSetsPageSizeFluently(): void
    {
        $table = $this->createTable()->pageSize(50);

        $this->assertSame(50, $table->getPageSize());
    }

    #[Test]
    public function itHasDefaultPageSize(): void
    {
        $table = $this->createTable();

        $this->assertSame(25, $table->getPageSize());
    }

    #[Test]
    public function itDefaultsSearchableToFalse(): void
    {
        $table = $this->createTable();

        $this->assertFalse($table->isSearchable());
    }

    #[Test]
    public function itSetsSearchableFluently(): void
    {
        $table = $this->createTable()->searchable();

        $this->assertTrue($table->isSearchable());
    }

    #[Test]
    public function itEvaluatesClosureSearchable(): void
    {
        $table = $this->createTable()->searchable(fn () => true);

        $this->assertTrue($table->isSearchable());
    }

    #[Test]
    public function itEvaluatesRawValue(): void
    {
        $table = $this->createTable();

        $this->assertSame('raw', $table->evaluate('raw'));
        $this->assertNull($table->evaluate(null));
    }

    #[Test]
    public function itEvaluatesClosureValue(): void
    {
        $table = $this->createTable();

        $this->assertSame('resolved', $table->evaluate(fn () => 'resolved'));
    }

    #[Test]
    public function itResolvesQueryBuilderFromString(): void
    {
        $qb = $this->createMock(QueryBuilder::class);
        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects($this->once())
            ->method('createQueryBuilder')
            ->willReturn($qb);
        $qb->expects($this->once())->method('select')->with('o')->willReturn($qb);
        $qb->expects($this->once())->method('from')->with('App\Entity\User', 'o')->willReturn($qb);

        $adapter = $this->createMock(DoctrineTableAdapter::class);
        $table = new Table($em, $adapter);
        $table->query('App\Entity\User');

        $result = $this->invokeResolveQueryBuilder($table);

        $this->assertSame($qb, $result);
    }

    #[Test]
    public function itClonesExistingQueryBuilder(): void
    {
        $qb = $this->createMock(QueryBuilder::class);

        $table = $this->createTable();
        $table->query($qb);

        $result = $this->invokeResolveQueryBuilder($table);

        $this->assertNotSame($qb, $result);
    }

    #[Test]
    public function itThrowsWhenNoQueryConfigured(): void
    {
        $table = $this->createTable();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('No query or entity class configured for table.');

        $this->invokeResolveQueryBuilder($table);
    }

    #[Test]
    public function itReturnsResultsViaGetResults(): void
    {
        $qb = $this->createMock(QueryBuilder::class);
        $em = $this->createMock(EntityManagerInterface::class);
        $em->method('createQueryBuilder')->willReturn($qb);
        $qb->method('select')->willReturn($qb);
        $qb->method('from')->willReturn($qb);

        $adapter = $this->createMock(DoctrineTableAdapter::class);
        $adapter->expects($this->once())->method('applyFilters')->with($qb, [], []);
        $adapter->expects($this->once())->method('applySort')->with($qb, 'id', 'asc');
        $adapter->expects($this->once())->method('paginate')->with($qb, 1, 25)
            ->willReturn(new DataTableResult([], 0, 1, 25));

        $table = new Table($em, $adapter);
        $table->query('App\Entity\User');

        $request = new Request();
        $result = $table->getResults($request);

        $this->assertInstanceOf(DataTableResult::class, $result);
        $this->assertSame(0, $result->getTotal());
    }

    private function createTable(): Table
    {
        return new Table(
            $this->createMock(EntityManagerInterface::class),
            $this->createMock(DoctrineTableAdapter::class),
        );
    }

    private function invokeResolveQueryBuilder(Table $table): QueryBuilder
    {
        $ref = new \ReflectionMethod($table, 'resolveQueryBuilder');

        /* @var QueryBuilder */
        return $ref->invoke($table);
    }
}
