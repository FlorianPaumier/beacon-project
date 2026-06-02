<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Crud;

use Devgeek\BeaconAdmin\Crud\DataTable\Column\Column;
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

    /** @var array<string> */
    protected array $sortableFields = [];

    /** @var array<string> */
    protected array $searchableFields = [];

    protected int $pageSize = 25;

    protected string $entityLabel = '';

    protected string $entityLabelPlural = '';

    protected ?\Closure $repositoryMethod = null;

    /** @var array<\Closure(QueryBuilder): void> */
    protected array $queryModifiers = [];

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

    public function pageSize(int $pageSize): static
    {
        $this->pageSize = $pageSize;

        return $this;
    }

    public function getPageSize(): int
    {
        return $this->pageSize;
    }
}
