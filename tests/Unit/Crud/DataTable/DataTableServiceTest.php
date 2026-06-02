<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Crud\DataTable;

use Devgeek\BeaconAdmin\Crud\CrudConfig;
use Devgeek\BeaconAdmin\Crud\DataTable\DataTableService;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

final class DataTableServiceTest extends TestCase
{
    private static ?EntityManager $em = null;

    protected function setUp(): void
    {
        if (self::$em !== null) {
            return;
        }

        $config = ORMSetup::createAttributeMetadataConfiguration(
            [__DIR__.'/../../../../tests/Fixtures/TestApp/Entity'],
            true,
        );
        $config->enableNativeLazyObjects(true);

        $connection = DriverManager::getConnection(['driver' => 'pdo_sqlite', 'memory' => true], $config);
        self::$em = new EntityManager($connection, $config);

        $schemaTool = new \Doctrine\ORM\Tools\SchemaTool(self::$em);
        $schemaTool->createSchema([self::$em->getClassMetadata(
            \Devgeek\BeaconAdmin\Tests\Fixtures\TestApp\Entity\TestEntity::class,
        )]);
    }

    private function createQueryBuilder(): QueryBuilder
    {
        return self::$em->createQueryBuilder()
            ->select('e')
            ->from(\Devgeek\BeaconAdmin\Tests\Fixtures\TestApp\Entity\TestEntity::class, 'e');
    }

    #[Test]
    public function itAppliesSearchFilter(): void
    {
        $config = $this->createMock(CrudConfig::class);
        $config->method('getSearchableFields')->willReturn(['name', 'email']);
        $config->method('getSortableFields')->willReturn([]);
        $config->method('getPageSize')->willReturn(25);

        $qb = $this->createQueryBuilder();
        $request = new Request(['search' => 'john']);
        $service = new DataTableService();

        $result = $service->process($qb, $request, $config);

        $this->assertSame('john', $result->getSearch());
        $this->assertStringContainsString('LIKE', $qb->getDQL());
        $this->assertStringContainsString('search_0', $qb->getDQL());
        $this->assertStringContainsString('search_1', $qb->getDQL());
    }

    #[Test]
    public function itAppliesSorting(): void
    {
        $config = $this->createMock(CrudConfig::class);
        $config->method('getSearchableFields')->willReturn([]);
        $config->method('getSortableFields')->willReturn(['name', 'email']);
        $config->method('getPageSize')->willReturn(25);

        $qb = $this->createQueryBuilder();
        $request = new Request(['sort' => 'name', 'dir' => 'asc']);
        $service = new DataTableService();

        $result = $service->process($qb, $request, $config);

        $this->assertSame('name', $result->getSortField());
        $this->assertSame('asc', $result->getSortDir());
    }

    #[Test]
    public function itAppliesSortingDescending(): void
    {
        $config = $this->createMock(CrudConfig::class);
        $config->method('getSearchableFields')->willReturn([]);
        $config->method('getSortableFields')->willReturn(['name']);
        $config->method('getPageSize')->willReturn(25);

        $qb = $this->createQueryBuilder();
        $request = new Request(['sort' => 'name', 'dir' => 'desc']);
        $service = new DataTableService();

        $result = $service->process($qb, $request, $config);

        $this->assertSame('desc', $result->getSortDir());
    }

    #[Test]
    public function itIgnoresSortForNonSortableFields(): void
    {
        $config = $this->createMock(CrudConfig::class);
        $config->method('getSearchableFields')->willReturn([]);
        $config->method('getSortableFields')->willReturn(['name']);
        $config->method('getPageSize')->willReturn(25);

        $qb = $this->createQueryBuilder();
        $request = new Request(['sort' => 'password']);
        $service = new DataTableService();
        $result = $service->process($qb, $request, $config);

        $this->assertSame('password', $result->getSortField());
    }

    #[Test]
    public function itPaginatesResults(): void
    {
        $config = $this->createMock(CrudConfig::class);
        $config->method('getSearchableFields')->willReturn([]);
        $config->method('getSortableFields')->willReturn([]);
        $config->method('getPageSize')->willReturn(10);

        $qb = $this->createQueryBuilder();
        $request = new Request(['page' => '3']);
        $service = new DataTableService();
        $result = $service->process($qb, $request, $config);

        $this->assertSame(3, $result->getPage());
        $this->assertSame(10, $result->getPerPage());
    }

    #[Test]
    public function itDefaultsPageToOne(): void
    {
        $config = $this->createMock(CrudConfig::class);
        $config->method('getSearchableFields')->willReturn([]);
        $config->method('getSortableFields')->willReturn([]);
        $config->method('getPageSize')->willReturn(25);

        $qb = $this->createQueryBuilder();
        $request = new Request();
        $service = new DataTableService();
        $result = $service->process($qb, $request, $config);

        $this->assertSame(1, $result->getPage());
        $this->assertSame(25, $result->getPerPage());
    }

    #[Test]
    public function itReturnsDataTableResult(): void
    {
        $config = $this->createMock(CrudConfig::class);
        $config->method('getSearchableFields')->willReturn([]);
        $config->method('getSortableFields')->willReturn([]);
        $config->method('getPageSize')->willReturn(25);

        $qb = $this->createQueryBuilder();
        $request = new Request(['page' => '2', 'search' => 'test']);
        $service = new DataTableService();

        $result = $service->process($qb, $request, $config);

        $this->assertSame(2, $result->getPage());
        $this->assertSame(25, $result->getPerPage());
        $this->assertSame('test', $result->getSearch());
        $this->assertSame(0, $result->getTotal());
    }
}
