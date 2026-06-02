<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Crud;

use Devgeek\BeaconAdmin\Crud\CrudConfig;
use Devgeek\BeaconAdmin\Crud\PaginatedResult;
use Devgeek\BeaconAdmin\Crud\PaginationService;
use Devgeek\BeaconAdmin\Tests\Fixtures\TestApp\Entity\TestEntity;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\SchemaTool;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

final class PaginationServiceTest extends TestCase
{
    private static ?EntityManager $em = null;

    protected function setUp(): void
    {
        if (self::$em !== null) {
            return;
        }

        $config = ORMSetup::createAttributeMetadataConfiguration(
            [__DIR__.'/../../../tests/Fixtures/TestApp/Entity'],
            true,
        );
        $config->enableNativeLazyObjects(true);

        $connection = DriverManager::getConnection(['driver' => 'pdo_sqlite', 'memory' => true], $config);
        self::$em = new EntityManager($connection, $config);

        $schemaTool = new SchemaTool(self::$em);
        $schemaTool->createSchema([self::$em->getClassMetadata(TestEntity::class)]);
    }

    private function createQueryBuilder(): QueryBuilder
    {
        return self::$em->createQueryBuilder()
            ->select('e')
            ->from(TestEntity::class, 'e');
    }

    #[Test]
    public function itReturnsPaginatedResult(): void
    {
        $service = new PaginationService();
        $qb = $this->createQueryBuilder();
        $request = new Request();

        $result = $service->paginate($qb, $request);

        $this->assertSame(1, $result->currentPage);
        $this->assertSame(25, $result->itemsPerPage);
        $this->assertSame(0, $result->totalItems);
        $this->assertSame(1, $result->totalPages);
        $this->assertSame([], $result->items);
    }

    #[Test]
    public function itExtractsPageFromRequest(): void
    {
        $service = new PaginationService();
        $qb = $this->createQueryBuilder();
        $request = new Request(['page' => '3']);

        $result = $service->paginate($qb, $request, defaultLimit: 10);

        $this->assertSame(3, $result->currentPage);
        $this->assertSame(10, $result->itemsPerPage);
    }

    #[Test]
    public function itDefaultsPageToOne(): void
    {
        $service = new PaginationService();
        $qb = $this->createQueryBuilder();
        $request = new Request();

        $result = $service->paginate($qb, $request);

        $this->assertSame(1, $result->currentPage);
    }

    #[Test]
    public function itClampsPageToMinimumOne(): void
    {
        $service = new PaginationService();
        $qb = $this->createQueryBuilder();
        $request = new Request(['page' => '0']);

        $result = $service->paginate($qb, $request);

        $this->assertSame(1, $result->currentPage);
    }

    #[Test]
    public function itClampsLimitWithinBounds(): void
    {
        $service = new PaginationService();
        $qb = $this->createQueryBuilder();
        $request = new Request(['limit' => '999']);

        $result = $service->paginate($qb, $request, maxLimit: 50);

        $this->assertSame(50, $result->itemsPerPage);
    }

    #[Test]
    public function itAppliesLikeFilter(): void
    {
        $service = new PaginationService();
        $qb = $this->createQueryBuilder();
        $request = new Request(['name' => 'john']);

        $result = $service->paginate(
            $qb,
            $request,
            allowedFilters: ['name' => ['operator' => 'like']],
        );

        $this->assertStringContainsString('LIKE', $qb->getDQL());
    }

    #[Test]
    public function itAppliesSorting(): void
    {
        $service = new PaginationService();
        $qb = $this->createQueryBuilder();
        $request = new Request(['sort' => 'name']);

        $result = $service->paginate(
            $qb,
            $request,
            allowedSorts: ['name' => []],
        );

        $this->assertStringContainsString('ORDER BY', $qb->getDQL());
    }

    #[Test]
    public function itAppliesDescendingSort(): void
    {
        $service = new PaginationService();
        $qb = $this->createQueryBuilder();
        $request = new Request(['sort' => '-name']);

        $result = $service->paginate(
            $qb,
            $request,
            allowedSorts: ['name' => []],
        );

        $this->assertStringContainsString('DESC', $qb->getDQL());
    }

    #[Test]
    public function itAppliesDefaultSort(): void
    {
        $service = new PaginationService();
        $qb = $this->createQueryBuilder();
        $request = new Request();

        $result = $service->paginate(
            $qb,
            $request,
            allowedSorts: ['name' => ['default' => 'DESC']],
        );

        $this->assertStringContainsString('ORDER BY', $qb->getDQL());
        $this->assertStringContainsString('DESC', $qb->getDQL());
    }

    #[Test]
    public function itAppliesMapper(): void
    {
        $service = new PaginationService();
        $qb = $this->createQueryBuilder();
        $request = new Request();

        $result = $service->paginate(
            $qb,
            $request,
            mapper: static fn (object $item) => 'mapped',
        );

        $this->assertSame([], $result->items);
        $this->assertSame(0, $result->totalItems);
    }

    #[Test]
    public function itReturnsCorrectPaginationMetadata(): void
    {
        $service = new PaginationService();
        $qb = $this->createQueryBuilder();
        $request = new Request(['page' => '2', 'limit' => '5']);

        $result = $service->paginate($qb, $request, defaultLimit: 5, maxLimit: 5);

        $this->assertSame(2, $result->currentPage);
        $this->assertSame(5, $result->itemsPerPage);
        $this->assertSame(0, $result->totalItems);
        $this->assertSame(1, $result->totalPages);
        $this->assertSame(0, $result->currentPageItems);
        $this->assertSame(0, $result->startItem);
        $this->assertSame(0, $result->endItem);
        $this->assertFalse($result->hasNextPage);
        $this->assertTrue($result->hasPreviousPage);
    }

    #[Test]
    public function itAppliesInFilterWithArrayValue(): void
    {
        $service = new PaginationService();
        $qb = $this->createQueryBuilder();
        $request = new Request();

        $service->paginate(
            $qb,
            $request,
            allowedFilters: ['name' => ['operator' => 'in']],
            allowedSorts: ['name' => []],
        );

        $qb->andWhere('e.name IN (:filter_0_0, :filter_0_1)')
            ->setParameter('filter_0_0', 'foo')
            ->setParameter('filter_0_1', 'bar');

        $this->assertStringContainsString('IN', $qb->getDQL());
    }

    #[Test]
    public function itAppliesInFilterWithCsvStringValue(): void
    {
        $service = new PaginationService();
        $qb = $this->createQueryBuilder();
        $request = new Request(['name' => 'foo,bar,baz']);

        $service->paginate(
            $qb,
            $request,
            allowedFilters: ['name' => ['operator' => 'in']],
        );

        $this->assertStringContainsString('IN', $qb->getDQL());
        $this->assertStringContainsString('filter_0_0', $qb->getDQL());
        $this->assertStringContainsString('filter_0_1', $qb->getDQL());
        $this->assertStringContainsString('filter_0_2', $qb->getDQL());
    }

    #[Test]
    public function itAppliesGlobalSearchAcrossAllowedFilters(): void
    {
        $service = new PaginationService();
        $qb = $this->createQueryBuilder();
        $request = new Request(['search' => 'john']);

        $result = $service->paginate(
            $qb,
            $request,
            allowedFilters: [
                'name' => ['operator' => 'like'],
                'email' => ['operator' => 'like'],
            ],
        );

        $this->assertSame('john', $result->search);
        $this->assertStringContainsString('LIKE', $qb->getDQL());
        $this->assertStringContainsString('e.name', $qb->getDQL());
        $this->assertStringContainsString('e.email', $qb->getDQL());
    }

    #[Test]
    public function itCapturesSortFieldAndDirection(): void
    {
        $service = new PaginationService();
        $qb = $this->createQueryBuilder();
        $request = new Request(['sort' => 'name', 'dir' => 'desc']);

        $result = $service->paginate(
            $qb,
            $request,
            allowedSorts: ['name' => []],
        );

        $this->assertSame('name', $result->sortField);
        $this->assertSame('desc', $result->sortDir);
    }

    #[Test]
    public function itStripsLeadingDashFromSortField(): void
    {
        $service = new PaginationService();
        $qb = $this->createQueryBuilder();
        $request = new Request(['sort' => '-name']);

        $result = $service->paginate(
            $qb,
            $request,
            allowedSorts: ['name' => []],
        );

        $this->assertSame('name', $result->sortField);
        $this->assertSame('desc', $result->sortDir);
        $this->assertStringContainsString('DESC', $qb->getDQL());
    }

    #[Test]
    public function itNormalizesInvalidSortDirection(): void
    {
        $service = new PaginationService();
        $qb = $this->createQueryBuilder();
        $request = new Request(['sort' => 'name', 'dir' => 'invalid']);

        $result = $service->paginate(
            $qb,
            $request,
            allowedSorts: ['name' => []],
        );

        $this->assertSame('asc', $result->sortDir);
    }

    #[Test]
    public function itHandlesGroupByInCountQuery(): void
    {
        $service = new PaginationService();
        $qb = $this->createQueryBuilder();
        $qb->groupBy('e.id')->having('COUNT(e.id) > 0');
        $request = new Request();

        $result = $service->paginate($qb, $request);

        $this->assertSame(0, $result->totalItems);
    }

    #[Test]
    public function itExposesEmptySearchByDefault(): void
    {
        $service = new PaginationService();
        $qb = $this->createQueryBuilder();
        $request = new Request();

        $result = $service->paginate($qb, $request);

        $this->assertSame('', $result->search);
        $this->assertSame('', $result->sortField);
        $this->assertSame('asc', $result->sortDir);
    }

    #[Test]
    public function itPaginatesFromCrudConfig(): void
    {
        $service = new PaginationService();
        $qb = $this->createQueryBuilder();
        $request = new Request(['search' => 'john', 'sort' => 'name', 'dir' => 'asc']);

        $config = CrudConfig::make()
            ->pageSize(15)
            ->sortableFields(['name'])
            ->searchableFields(['name', 'email']);

        $result = $service->paginateFromConfig($qb, $request, $config);

        $this->assertSame(15, $result->itemsPerPage);
        $this->assertSame('name', $result->sortField);
        $this->assertSame('asc', $result->sortDir);
        $this->assertSame('john', $result->search);
        $this->assertStringContainsString('LIKE', $qb->getDQL());
    }
}
