<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Crud;

use Devgeek\BeaconAdmin\Crud\DataTable\Column\Column;
use Devgeek\BeaconAdmin\Crud\DataTable\Column\ColumnGroup;
use Devgeek\BeaconAdmin\Crud\DataTable\Filter\FilterInterface;
use Devgeek\BeaconAdmin\Crud\Field\Field;
use Doctrine\ORM\QueryBuilder;

/** @phpstan-consistent-constructor */
class CrudConfig
{
    protected string $entityClass;

    /** @var array<string> */
    protected array $fields = [];

    /** @var array<Field> */
    protected array $fieldObjects = [];

    /** @var array<Column> */
    protected array $columns = [];

    /** @var array<ColumnGroup> */
    protected array $columnGroups = [];

    /** @var array<string> */
    protected array $sortableFields = [];

    /** @var array<string> */
    protected array $searchableFields = [];

    /** @var array<string> */
    protected array $showFields = [];

    protected int $pageSize = 25;

    protected string $entityLabel = '';

    protected string $entityLabelPlural = '';

    protected ?\Closure $repositoryMethod = null;

    /** @var array<\Closure(QueryBuilder): void> */
    protected array $queryModifiers = [];

    /** @var array<string, array{operator?: string, type?: string, field?: string, choices?: array<string, string>, class?: class-string}> */
    protected array $filters = [];

    /** @var array<FilterInterface> */
    protected array $filterObjects = [];

    public function repositoryMethod(?\Closure $callback): static
    {
        $this->repositoryMethod = $callback;

        return $this;
    }

    public function getRepositoryMethod(): ?\Closure
    {
        return $this->repositoryMethod;
    }

    public function modifyQuery(\Closure $callback): static
    {
        $this->queryModifiers[] = $callback;

        return $this;
    }

    /** @return array<\Closure(QueryBuilder): void> */
    public function getQueryModifiers(): array
    {
        return $this->queryModifiers;
    }

    public function applyQueryModifiers(QueryBuilder $queryBuilder): void
    {
        foreach ($this->queryModifiers as $modifier) {
            $modifier($queryBuilder);
        }
    }

    public static function make(): static
    {
        return new static();
    }

    /** @param class-string $entityClass */
    public function entityClass(string $entityClass): static
    {
        $this->entityClass = $entityClass;

        return $this;
    }

    /** @return class-string */
    public function getEntityClass(): string
    {
        return $this->entityClass;
    }

    public function hasEntityClass(): bool
    {
        return isset($this->entityClass);
    }

    /** @param array<string> $fields */
    public function fields(array $fields): static
    {
        $this->fields = $fields;

        return $this;
    }

    /** @return array<string> */
    public function getFields(): array
    {
        return $this->fields;
    }

    public function field(Field $field): static
    {
        $this->fieldObjects[] = $field;

        return $this;
    }

    /** @return array<Field> */
    public function getFieldObjects(): array
    {
        return $this->fieldObjects;
    }

    public function column(Column $column): static
    {
        $this->columns[] = $column;

        return $this;
    }

    /** @return array<Column> */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /** @param array<ColumnGroup> $groups */
    public function columnGroups(array $groups): static
    {
        $this->columnGroups = $groups;

        return $this;
    }

    public function columnGroup(ColumnGroup $group): static
    {
        $this->columnGroups[] = $group;

        return $this;
    }

    /** @return array<ColumnGroup> */
    public function getColumnGroups(): array
    {
        return $this->columnGroups;
    }

    /**
     * Returns all column names that belong to at least one group.
     *
     * @return array<string>
     */
    public function getGroupedColumnNames(): array
    {
        $names = [];

        foreach ($this->columnGroups as $group) {
            foreach ($group->getColumns() as $colName) {
                $names[] = $colName;
            }
        }

        return array_unique($names);
    }

    /**
     * Returns columns that are NOT in any group.
     *
     * @return array<Column>
     */
    public function getUngroupedColumns(): array
    {
        $grouped = $this->getGroupedColumnNames();

        return array_filter(
            $this->columns,
            static fn (Column $col) => !\in_array($col->getName(), $grouped, true),
        );
    }

    public function entityLabel(string $entityLabel): static
    {
        $this->entityLabel = $entityLabel;

        return $this;
    }

    public function getEntityLabel(): string
    {
        return $this->entityLabel;
    }

    public function entityLabelPlural(string $entityLabelPlural): static
    {
        $this->entityLabelPlural = $entityLabelPlural;

        return $this;
    }

    public function getEntityLabelPlural(): string
    {
        return $this->entityLabelPlural;
    }

    /** @param array<string> $fields */
    public function sortableFields(array $fields): static
    {
        $this->sortableFields = $fields;

        return $this;
    }

    /** @return array<string> */
    public function getSortableFields(): array
    {
        return $this->sortableFields;
    }

    /** @param array<string> $fields */
    public function searchableFields(array $fields): static
    {
        $this->searchableFields = $fields;

        return $this;
    }

    /** @return array<string> */
    public function getSearchableFields(): array
    {
        return $this->searchableFields;
    }

    /** @param array<string> $fields */
    public function showFields(array $fields): static
    {
        $this->showFields = $fields;

        return $this;
    }

    /** @return array<string> */
    public function getShowFields(): array
    {
        if ($this->showFields !== []) {
            return $this->showFields;
        }

        $names = [];
        foreach ($this->columns as $column) {
            $names[] = $column->getName();
        }

        return $names;
    }

    public function pageSize(int $pageSize): static
    {
        $this->pageSize = $pageSize;

        return $this;
    }

    public function getPageSize(): int
    {
        return $this->pageSize;
    }

    public function booleanFilter(string $field): static
    {
        $this->filters[$field] = ['operator' => 'eq', 'type' => 'boolean'];

        return $this;
    }

    /** @param array<string, string> $choices */
    public function choiceFilter(string $field, array $choices, string $operator = 'eq'): static
    {
        $this->filters[$field] = [
            'operator' => $operator,
            'type' => 'choice',
            'choices' => $choices,
        ];

        return $this;
    }

    public function dateFilter(string $field, string $operator = 'between'): static
    {
        $this->filters[$field] = ['operator' => $operator, 'type' => 'datetime'];

        return $this;
    }

    public function datetimeFilter(string $field, string $operator = 'between'): static
    {
        return $this->dateFilter($field, $operator);
    }

    /** @param class-string $targetEntity */
    public function entityFilter(string $field, string $targetEntity, ?string $label = null): static
    {
        $entry = [
            'operator' => 'eq',
            'type' => 'entity',
            'class' => $targetEntity,
        ];

        if (null !== $label) {
            $entry['label'] = $label;
        }

        $this->filters[$field] = $entry;

        return $this;
    }

    /** @return array<string, array{operator?: string, type?: string, field?: string, choices?: array<string, string>, class?: class-string, label?: ?string}> */
    public function getFilters(): array
    {
        $merged = $this->filters;

        $collisions = array_intersect_key($merged, array_flip(array_map(static fn ($f) => $f->getField(), $this->filterObjects)));
        if ($collisions !== []) {
            trigger_error(
                sprintf('Filter key collision detected: "%s". The filter object takes precedence.', implode('", "', array_keys($collisions))),
                E_USER_WARNING,
            );
        }

        foreach ($this->filterObjects as $filter) {
            $merged[$filter->getField()] = $filter->getMeta();
        }

        return $merged;
    }

    public function filter(FilterInterface $filter): static
    {
        $this->filterObjects[] = $filter;

        return $this;
    }
}
