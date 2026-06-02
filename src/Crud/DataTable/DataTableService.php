<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Crud\DataTable;

use Devgeek\BeaconAdmin\Crud\CrudConfig;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\Request;

class DataTableService
{
    public function process(QueryBuilder $queryBuilder, Request $request, CrudConfig $config): DataTableResult
    {
        $page = max(1, (int) $request->query->get('page', '1'));
        $perPage = $config->getPageSize();
        $sortField = $request->query->get('sort', '');
        $sortDir = strtolower($request->query->get('dir', 'asc'));
        $search = $request->query->get('search', '');

        $alias = $queryBuilder->getRootAliases()[0] ?? 'e';

        if ($search !== '' && $config->getSearchableFields() !== []) {
            $this->applySearch($queryBuilder, $search, $config->getSearchableFields(), $alias);
        }

        if ($sortField !== '' && in_array($sortField, $config->getSortableFields(), true)) {
            $direction = $sortDir === 'desc' ? 'DESC' : 'ASC';
            $queryBuilder->orderBy($alias.'.'.$sortField, $direction);
        }

        $queryBuilder->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage);

        $paginator = new Paginator($queryBuilder);
        $total = count($paginator);

        /** @var array<int, object> $results */
        $results = iterator_to_array($paginator);

        return new DataTableResult(
            results: $results,
            total: $total,
            page: $page,
            perPage: $perPage,
            totalPages: (int) ceil($total / $perPage),
            sortField: $sortField,
            sortDir: $sortDir,
            search: $search,
        );
    }

    /**
     * @param array<string> $fields
     */
    private function applySearch(QueryBuilder $queryBuilder, string $search, array $fields, string $alias): void
    {
        $conditions = [];
        foreach ($fields as $index => $field) {
            $paramName = 'search_'.$index;
            $conditions[] = $queryBuilder->expr()->like($alias.'.'.$field, ':'.$paramName);
            $queryBuilder->setParameter($paramName, '%'.$search.'%');
        }

        $queryBuilder->andWhere($queryBuilder->expr()->orX(...$conditions));
    }
}
