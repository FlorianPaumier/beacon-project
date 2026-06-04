<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Crud;

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;

final readonly class PaginationService
{
    private const int DEFAULT_LIMIT = 25;
    private const int MAX_LIMIT = 100;
    private const int MAX_ENTITY_OPTIONS = 1000;

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

        $allowedFilters = $this->buildSearchFilters($config->getSearchableFields());
        foreach ($config->getFilters() as $name => $filter) {
            $allowedFilters[$name] = $filter;
        }

        $this->stashEntityFilterOptions($queryBuilder, $request, $config);

        return $this->paginate(
            $queryBuilder,
            $request,
            defaultLimit: $config->getPageSize(),
            allowedFilters: $allowedFilters,
            allowedSorts: $allowedSorts,
            mapper: $mapper,
            entityClass: $config->hasEntityClass() ? $config->getEntityClass() : null,
        );
    }

    private function stashEntityFilterOptions(QueryBuilder $queryBuilder, Request $request, CrudConfig $config): void
    {
        $entityOptions = [];
        foreach ($config->getFilters() as $name => $filter) {
            if (($filter['type'] ?? null) !== 'entity' || !isset($filter['class'])) {
                continue;
            }

            $class = $filter['class'];
            try {
                $repository = $queryBuilder->getEntityManager()->getRepository($class);
            } catch (\Throwable) {
                continue;
            }

            $labelField = $filter['label'] ?? $this->resolveLabelField($class);

            if (null !== $labelField) {
                $rows = $queryBuilder->getEntityManager()->createQuery(
                    'SELECT e.id AS id, e.'.$labelField.' AS label FROM '.$class.' e'
                )->getArrayResult();

                $options = [];
                foreach ($rows as $row) {
                    $options[(string) ($row['id'] ?? '')] = (string) ($row['label'] ?? '');
                }
            } else {
                $entities = $repository->findBy([], null, self::MAX_ENTITY_OPTIONS);
                $options = [];
                foreach ($entities as $entity) {
                    $options[(string) $this->extractEntityIdentifier($entity)] = $this->extractEntityLabel($entity);
                }
            }

            $entityOptions[$name] = $options;
        }

        if ($entityOptions !== []) {
            $request->attributes->set('_beacon_admin_entity_filter_options', $entityOptions);
        }
    }

    private function resolveLabelField(string $class): ?string
    {
        static $cache = [];

        if (array_key_exists($class, $cache)) {
            return $cache[$class];
        }

        if (method_exists($class, '__toString')) {
            $cache[$class] = null;

            return null;
        }

        $refl = new \ReflectionClass($class);
        $properties = $refl->getProperties();
        usort($properties, static fn ($a, $b) => $a->getName() <=> $b->getName());
        foreach ($properties as $property) {
            $type = $property->getType();
            if ($type instanceof \ReflectionNamedType && 'string' === $type->getName()) {
                $cache[$class] = $property->getName();

                return $property->getName();
            }
        }

        $cache[$class] = null;

        return null;
    }

    private function extractEntityIdentifier(object $entity): mixed
    {
        $refl = new \ReflectionClass($entity);
        if ($refl->hasMethod('getId')) {
            $method = $refl->getMethod('getId');
            if ($method->getNumberOfRequiredParameters() === 0) {
                return $method->invoke($entity);
            }
        }

        return spl_object_id($entity);
    }

    private function extractEntityLabel(object $entity): string
    {
        if (method_exists($entity, '__toString')) {
            $label = (string) $entity;

            if ($label !== '') {
                return $label;
            }
        }

        $refl = new \ReflectionClass($entity);
        $properties = $refl->getProperties();
        usort($properties, static fn ($a, $b) => $a->getName() <=> $b->getName());
        foreach ($properties as $property) {
            $type = $property->getType();
            if (!$type instanceof \ReflectionNamedType || $type->getName() !== 'string') {
                continue;
            }
            $value = $property->getValue($entity);

            return is_string($value) ? $value : '#'.$this->extractEntityIdentifier($entity);
        }

        return '#'.$this->extractEntityIdentifier($entity);
    }

    /**
     * @param array<string> $searchableFields
     *
     * @return array<string, array{operator: string, type: string}>
     */
    private function buildSearchFilters(array $searchableFields): array
    {
        $filters = [];
        foreach ($searchableFields as $field) {
            $filters[$field] = ['operator' => 'like', 'type' => 'string'];
        }

        return $filters;
    }

    /**
     * @param array<string, array{operator?: string, type?: string, field?: string}> $allowedFilters
     * @param array<string, array{default?: string}>                                 $allowedSorts
     * @param class-string|null                                                      $entityClass
     */
    public function paginate(
        QueryBuilder $queryBuilder,
        Request $request,
        int $defaultLimit = self::DEFAULT_LIMIT,
        int $maxLimit = self::MAX_LIMIT,
        /* @var array<string, array{operator?: string, type?: string, field?: string}> */
        array $allowedFilters = [],
        /* @var array<string, array{default?: string}> */
        array $allowedSorts = [],
        ?callable $mapper = null,
        ?string $entityClass = null,
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

        $queryBuilder = $this->applyFilters($queryBuilder, $request, $allowedFilters, $search, $entityClass, $queryBuilder->getEntityManager());
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

    /**
     * @param array<string, array{operator?: string, type?: string, field?: string}> $allowedFilters
     * @param class-string|null                                                      $entityClass
     */
    private function applyFilters(
        QueryBuilder $queryBuilder,
        Request $request,
        array $allowedFilters,
        string $search,
        ?string $entityClass,
        \Doctrine\ORM\EntityManagerInterface $em,
    ): QueryBuilder {
        if ($allowedFilters === [] && $search === '') {
            return $queryBuilder;
        }

        $alias = $this->getEntityAlias($queryBuilder);
        $parameterIndex = 0;

        if ($search !== '' && $allowedFilters !== []) {
            $paramName = 'search_'.$parameterIndex++;
            $expr = $queryBuilder->expr()->orX();
            foreach (array_keys($allowedFilters) as $field) {
                $filterType = $allowedFilters[$field]['type'] ?? 'string';
                if ($filterType !== 'string') {
                    continue;
                }
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

            $resolvedType = $this->resolveFieldType($em, $entityClass, $field, $fieldType);
            $this->applyFilterToQuery($queryBuilder, $operator, $fieldPath, $paramName, $value, $resolvedType);
        }

        return $queryBuilder;
    }

    /**
     * @param class-string|null $entityClass
     */
    private function resolveFieldType(\Doctrine\ORM\EntityManagerInterface $em, ?string $entityClass, string $field, string $declaredType): string
    {
        if (!in_array($declaredType, ['date', 'datetime'], true)) {
            return $declaredType;
        }

        if (null === $entityClass || str_contains($field, '.')) {
            return $declaredType;
        }

        try {
            $metadata = $em->getClassMetadata($entityClass);
        } catch (\Throwable) {
            return $declaredType;
        }

        if (!$metadata->hasField($field)) {
            return $declaredType;
        }

        $mapping = $metadata->getFieldMapping($field);
        $doctrineType = $mapping['type'] ?? null;

        if (in_array($doctrineType, ['datetime_immutable', 'date_immutable'], true)) {
            return 'datetime';
        }

        if (in_array($doctrineType, ['datetime', 'date'], true)) {
            return 'date';
        }

        return $declaredType;
    }

    /**
     * @param array<string, array{operator?: string, type?: string, field?: string}> $allowedFilters
     *
     * @return array<string, mixed>
     */
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
            $min = $value[0];
            $max = $value[1];
            if ($min === '' || $min === null || $max === '' || $max === null) {
                return $queryBuilder;
            }
            $queryBuilder
                ->andWhere(sprintf('%s BETWEEN :%s_min AND :%s_max', $fieldPath, $paramName, $paramName))
                ->setParameter($paramName.'_min', $this->castValue($min, $fieldType))
                ->setParameter($paramName.'_max', $this->castValue($max, $fieldType));
        }

        return $queryBuilder;
    }

    /**
     * @param array<string, array{default?: string}> $allowedSorts
     */
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
            'date' => (function () use ($value) {
                try {
                    return new \DateTime((string) $value);
                } catch (\Throwable) {
                    return null;
                }
            })(),
            'datetime' => (function () use ($value) {
                try {
                    return new \DateTimeImmutable((string) $value);
                } catch (\Throwable) {
                    return null;
                }
            })(),
            'float' => (float) $value,
            default => (string) $value,
        };
    }
}
