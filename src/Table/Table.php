<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Table;

use Devgeek\BeaconAdmin\Table\Column\TextColumn;
use Devgeek\BeaconAdmin\Table\Filter\Filter;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;

class Table
{
    protected QueryBuilder|string|null $query = null;

    /** @var array<TextColumn> */
    protected array $columns = [];

    /** @var array<Filter> */
    protected array $filters = [];

    protected string $defaultSortField = 'id';

    protected string $defaultSortDirection = 'asc';

    protected int $pageSize = 25;

    protected bool|\Closure $searchable = false;

    public function __construct(
        protected readonly EntityManagerInterface $entityManager,
        protected readonly DoctrineTableAdapter $adapter,
    ) {
    }

    public function query(QueryBuilder|string $query): static
    {
        $this->query = $query;

        return $this;
    }

    public function getQuery(): QueryBuilder|string|null
    {
        return $this->query;
    }

    /** @param array<TextColumn> $columns */
    public function columns(array $columns): static
    {
        $this->columns = $columns;

        return $this;
    }

    /** @return array<TextColumn> */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /** @param array<Filter> $filters */
    public function filters(array $filters): static
    {
        $this->filters = $filters;

        return $this;
    }

    /** @return array<Filter> */
    public function getFilters(): array
    {
        return $this->filters;
    }

    public function defaultSort(string $field, string $direction = 'asc'): static
    {
        $this->defaultSortField = $field;
        $this->defaultSortDirection = $direction;

        return $this;
    }

    public function getDefaultSortField(): string
    {
        return $this->defaultSortField;
    }

    public function getDefaultSortDirection(): string
    {
        return $this->defaultSortDirection;
    }

    public function pageSize(int $size): static
    {
        $this->pageSize = $size;

        return $this;
    }

    public function getPageSize(): int
    {
        return $this->pageSize;
    }

    public function searchable(bool|\Closure $searchable = true): static
    {
        $this->searchable = $searchable;

        return $this;
    }

    public function isSearchable(): bool
    {
        return (bool) $this->evaluate($this->searchable);
    }

    public function evaluate(mixed $value): mixed
    {
        return $value instanceof \Closure ? $value() : $value;
    }

    public function getResults(Request $request): DataTableResult
    {
        $queryBuilder = $this->resolveQueryBuilder();

        $page = $request->query->getInt('page', 1);
        $sortField = $request->query->get('sort', $this->defaultSortField);
        $sortDirection = $request->query->get('direction', $this->defaultSortDirection);

        $filterValues = $request->query->all('filters');

        $this->adapter->applyFilters($queryBuilder, $this->filters, $filterValues);

        $this->adapter->applySort($queryBuilder, $sortField, $sortDirection);

        return $this->adapter->paginate($queryBuilder, $page, $this->pageSize);
    }

    protected function resolveQueryBuilder(): QueryBuilder
    {
        if ($this->query instanceof QueryBuilder) {
            return clone $this->query;
        }

        if (is_string($this->query)) {
            return $this->entityManager
                ->createQueryBuilder()
                ->select('o')
                ->from($this->query, 'o');
        }

        throw new \RuntimeException('No query or entity class configured for table.');
    }
}
