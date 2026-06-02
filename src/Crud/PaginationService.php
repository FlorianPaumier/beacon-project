<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Crud;

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;

final readonly class PaginationService
{
    private const int DEFAULT_LIMIT = 25;
    private const int MAX_LIMIT = 100;

    public function paginateFromConfig(
        QueryBuilder $queryBuilder,
        Request $request,
        CrudConfig $config,
        ?callable $mapper = null,
    ): PaginatedResult {
        $allowedSorts = [];
        foreach ($config->getSortableFields() as $field) {
            $allowedSorts[$field] = [];
        }

        $allowedFilters = [];
        foreach ($config->getSearchableFields() as $field) {
            $allowedFilters[$field] = ['operator' => 'like'];
        }

        return $this->paginate(
            $queryBuilder,
            $request,
            defaultLimit: $config->getPageSize(),
            allowedFilters: $allowedFilters,
            allowedSorts: $allowedSorts,
            mapper: $mapper,
        );
    }

    public function paginate(
        QueryBuilder $queryBuilder,
        Request $request,
        int $defaultLimit = self::DEFAULT_LIMIT,
        int $maxLimit = self::MAX_LIMIT,
        array $allowedFilters = [],
        array $allowedSorts = [],
        ?callable $mapper = null,
    ): PaginatedResult {
        $page = max(1, $request->query->getInt('page', 1));
        $limit = min($maxLimit, max(1, $request->query->getInt('limit', $defaultLimit)));

        $search = $request->query->get('search', '');
        $sortField = $request->query->get('sort', '');
        $sortDir = strtolower($request->query->get('dir', 'asc'));
        if ($sortDir !== 'asc' && $sortDir !== 'desc') {
            $sortDir = 'asc';
        }

        if (str_starts_with($sortField, '-')) {
            $sortField = substr($sortField, 1);
            $sortDir = 'desc';
        }

        $queryBuilder = $this->applyFilters($queryBuilder, $request, $allowedFilters, $search);
        $queryBuilder = $this->applySorting($queryBuilder, $request, $allowedSorts, $sortField, $sortDir);

        $countQueryBuilder = clone $queryBuilder;
        $alias = $this->getEntityAlias($queryBuilder);
        $countQueryBuilder->select('COUNT(DISTINCT '.$alias.')');
        $countQueryBuilder->resetDQLPart('orderBy');
        $countQueryBuilder->resetDQLPart('groupBy');
        $countQueryBuilder->resetDQLPart('having');

        $totalItems = (int) $countQueryBuilder->getQuery()->getSingleScalarResult();

        $queryBuilder->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        $rawResults = $queryBuilder->getQuery()->getResult();

        $results = $mapper !== null ? array_map($mapper, $rawResults) : $rawResults;
        $itemsOnCurrentPage = count($results);

        $totalPages = $totalItems > 0 ? (int) ceil($totalItems / $limit) : 1;
        $hasNextPage = $page < $totalPages;
        $hasPreviousPage = $page > 1;

        $startItem = $totalItems > 0 ? (($page - 1) * $limit) + 1 : 0;
        $endItem = $totalItems > 0 ? min($startItem + $itemsOnCurrentPage - 1, $totalItems) : 0;

        return new PaginatedResult(
            items: $results,
            currentPage: $page,
            itemsPerPage: $limit,
            totalItems: $totalItems,
            totalPages: $totalPages,
            currentPageItems: $itemsOnCurrentPage,
            startItem: $startItem,
            endItem: $endItem,
            hasNextPage: $hasNextPage,
            hasPreviousPage: $hasPreviousPage,
            search: $search,
            sortField: $sortField,
            sortDir: $sortDir,
        );
    }

    private function applyFilters(QueryBuilder $queryBuilder, Request $request, array $allowedFilters, string $search): QueryBuilder
    {
        if ($allowedFilters === [] && $search === '') {
            return $queryBuilder;
        }

        $alias = $this->getEntityAlias($queryBuilder);
        $parameterIndex = 0;

        if ($search !== '' && $allowedFilters !== []) {
            $paramName = 'search_'.$parameterIndex++;
            $expr = $queryBuilder->expr()->orX();
            foreach (array_keys($allowedFilters) as $field) {
                $fieldPath = str_contains($field, '.') ? $field : $alias.'.'.$field;
                $expr->add(sprintf('LOWER(%s) LIKE LOWER(:%s)', $fieldPath, $paramName));
            }
            $queryBuilder->andWhere($expr);
            $queryBuilder->setParameter($paramName, '%'.$search.'%');
        }

        $filters = $this->extractFilters($request, $allowedFilters);

        foreach ($filters as $key => $value) {
            if (!isset($allowedFilters[$key]) || $value === '' || $value === null) {
                continue;
            }

            $config = $allowedFilters[$key];
            $operator = $config['operator'] ?? 'eq';
            $fieldType = $config['type'] ?? 'string';
            $paramName = 'filter_'.$parameterIndex++;
            $field = $config['field'] ?? $key;

            $fieldPath = str_contains($field, '.')
                ? $field
                : $alias.'.'.$field;

            $this->applyFilterToQuery($queryBuilder, $operator, $fieldPath, $paramName, $value, $fieldType);
        }

        return $queryBuilder;
    }

    private function extractFilters(Request $request, array $allowedFilters): array
    {
        $filterKeys = array_keys($allowedFilters);

        $filters = array_filter(
            $request->query->all(),
            static fn ($param): bool => in_array($param, $filterKeys, true),
            ARRAY_FILTER_USE_KEY,
        );

        $nestedFilters = $request->query->all('filter');
        if ($nestedFilters !== []) {
            $filters = array_merge($filters, $nestedFilters);
        }

        return $filters;
    }

    private function applyFilterToQuery(
        QueryBuilder $queryBuilder,
        string $operator,
        string $fieldPath,
        string $paramName,
        mixed $value,
        string $fieldType,
    ): void {
        match ($operator) {
            'like' => $queryBuilder
                ->andWhere(sprintf('LOWER(%s) LIKE LOWER(:%s)', $fieldPath, $paramName))
                ->setParameter($paramName, '%'.$value.'%'),

            'startsWith' => $queryBuilder
                ->andWhere(sprintf('LOWER(%s) LIKE LOWER(:%s)', $fieldPath, $paramName))
                ->setParameter($paramName, $value.'%'),

            'endsWith' => $queryBuilder
                ->andWhere(sprintf('LOWER(%s) LIKE LOWER(:%s)', $fieldPath, $paramName))
                ->setParameter($paramName, '%'.$value),

            'gt' => $queryBuilder
                ->andWhere(sprintf('%s > :%s', $fieldPath, $paramName))
                ->setParameter($paramName, $this->castValue($value, $fieldType)),

            'gte' => $queryBuilder
                ->andWhere(sprintf('%s >= :%s', $fieldPath, $paramName))
                ->setParameter($paramName, $this->castValue($value, $fieldType)),

            'lt' => $queryBuilder
                ->andWhere(sprintf('%s < :%s', $fieldPath, $paramName))
                ->setParameter($paramName, $this->castValue($value, $fieldType)),

            'lte' => $queryBuilder
                ->andWhere(sprintf('%s <= :%s', $fieldPath, $paramName))
                ->setParameter($paramName, $this->castValue($value, $fieldType)),

            'in' => $this->applyInFilter($queryBuilder, $fieldPath, $paramName, $value),

            'between' => $this->applyBetweenFilter($queryBuilder, $fieldPath, $paramName, $value, $fieldType),

            'isNull' => $queryBuilder->andWhere(sprintf('%s IS NULL', $fieldPath)),

            'isNotNull' => $queryBuilder->andWhere(sprintf('%s IS NOT NULL', $fieldPath)),

            default => $queryBuilder
                ->andWhere(sprintf('%s = :%s', $fieldPath, $paramName))
                ->setParameter($paramName, $this->castValue($value, $fieldType)),
        };
    }

    private function applyInFilter(QueryBuilder $queryBuilder, string $fieldPath, string $paramName, mixed $value): void
    {
        $values = is_array($value) ? $value : explode(',', (string) $value);
        $values = array_values(array_filter($values, static fn ($v) => $v !== '' && $v !== null));

        if ($values === []) {
            return;
        }

        $placeholders = [];
        foreach ($values as $index => $v) {
            $placeholder = $paramName.'_'.$index;
            $placeholders[] = ':'.$placeholder;
            $queryBuilder->setParameter($placeholder, $v);
        }

        $queryBuilder->andWhere(sprintf('%s IN (%s)', $fieldPath, implode(', ', $placeholders)));
    }

    private function applyBetweenFilter(
        QueryBuilder $queryBuilder,
        string $fieldPath,
        string $paramName,
        mixed $value,
        string $fieldType,
    ): QueryBuilder {
        if (is_array($value) && count($value) === 2) {
            $queryBuilder
                ->andWhere(sprintf('%s BETWEEN :%s_min AND :%s_max', $fieldPath, $paramName, $paramName))
                ->setParameter($paramName.'_min', $this->castValue($value[0], $fieldType))
                ->setParameter($paramName.'_max', $this->castValue($value[1], $fieldType));
        }

        return $queryBuilder;
    }

    private function applySorting(QueryBuilder $queryBuilder, Request $request, array $allowedSorts, string $sortField, string $sortDir): QueryBuilder
    {
        if ($allowedSorts === []) {
            return $queryBuilder;
        }

        $alias = $this->getEntityAlias($queryBuilder);

        if ($sortField === '') {
            foreach ($allowedSorts as $field => $config) {
                if (isset($config['default'])) {
                    $direction = strtoupper($config['default']) === 'DESC' ? 'DESC' : 'ASC';
                    $fieldPath = str_contains($field, '.') ? $field : $alias.'.'.$field;
                    $queryBuilder->addOrderBy($fieldPath, $direction);

                    break;
                }
            }

            return $queryBuilder;
        }

        $sortFields = explode(',', $sortField);

        foreach ($sortFields as $sortFieldPart) {
            $direction = $sortDir;
            $field = $sortFieldPart;

            if (str_starts_with($sortFieldPart, '-')) {
                $direction = 'DESC';
                $field = substr($sortFieldPart, 1);
            }

            if (!isset($allowedSorts[$field])) {
                continue;
            }

            $fieldPath = str_contains($field, '.') ? $field : $alias.'.'.$field;
            $queryBuilder->addOrderBy($fieldPath, $direction);
        }

        return $queryBuilder;
    }

    private function getEntityAlias(QueryBuilder $queryBuilder): string
    {
        $rootAliases = $queryBuilder->getRootAliases();

        return $rootAliases[0] ?? 'e';
    }

    private function castValue(mixed $value, string $type): mixed
    {
        return match ($type) {
            'int', 'integer' => (int) $value,
            'bool', 'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'date' => new \DateTime((string) $value),
            'datetime' => new \DateTimeImmutable((string) $value),
            'float' => (float) $value,
            default => (string) $value,
        };
    }
}
