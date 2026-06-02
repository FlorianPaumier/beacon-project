<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Table;

use Devgeek\BeaconAdmin\Table\Filter\Filter;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

class DoctrineTableAdapter
{
    public function __construct(
        protected readonly EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @param array<Filter>        $filters
     * @param array<string, mixed> $values
     */
    public function applyFilters(QueryBuilder $queryBuilder, array $filters, array $values): void
    {
        foreach ($filters as $filter) {
            $name = $filter->getName();

            if (array_key_exists($name, $values)) {
                $filter->apply($queryBuilder, $values[$name]);
            }
        }
    }

    public function applySort(QueryBuilder $queryBuilder, string $field, string $direction = 'asc'): void
    {
        $direction = strtolower($direction) === 'desc' ? 'DESC' : 'ASC';

        $queryBuilder->orderBy('o.'.$field, $direction);
    }

    public function paginate(QueryBuilder $queryBuilder, int $page, int $perPage): DataTableResult
    {
        $page = max(1, $page);
        $perPage = max(1, $perPage);

        $paginator = new Paginator($queryBuilder);

        $total = count($paginator);

        $queryBuilder
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage);

        $rows = iterator_to_array($paginator);

        return new DataTableResult($rows, $total, $page, $perPage);
    }
}
