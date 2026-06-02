<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Crud;

use Devgeek\BeaconAdmin\Crud\DataTable\Column\Column;
use Devgeek\BeaconAdmin\Crud\Field\Field;
use Doctrine\ORM\QueryBuilder;

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

    public function repositoryMethod(?\Closure $callback): self
    {
        $this->repositoryMethod = $callback;

        return $this;
    }

    public function getRepositoryMethod(): ?\Closure
    {
        return $this->repositoryMethod;
    }

    public function modifyQuery(\Closure $callback): self
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

    public static function make(): self
    {
        return new self();
    }

    /** @param class-string $entityClass */
    public function entityClass(string $entityClass): self
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
    public function fields(array $fields): self
    {
        $this->fields = $fields;

        return $this;
    }

    /** @return array<string> */
    public function getFields(): array
    {
        return $this->fields;
    }

    public function field(Field $field): self
    {
        $this->fieldObjects[] = $field;

        return $this;
    }

    /** @return array<Field> */
    public function getFieldObjects(): array
    {
        return $this->fieldObjects;
    }

    public function column(Column $column): self
    {
        $this->columns[] = $column;

        return $this;
    }

    /** @return array<Column> */
    public function getColumns(): array
    {
        return $this->columns;
    }

    public function entityLabel(string $entityLabel): self
    {
        $this->entityLabel = $entityLabel;

        return $this;
    }

    public function getEntityLabel(): string
    {
        return $this->entityLabel;
    }

    public function entityLabelPlural(string $entityLabelPlural): self
    {
        $this->entityLabelPlural = $entityLabelPlural;

        return $this;
    }

    public function getEntityLabelPlural(): string
    {
        return $this->entityLabelPlural;
    }

    /** @param array<string> $fields */
    public function sortableFields(array $fields): self
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
    public function searchableFields(array $fields): self
    {
        $this->searchableFields = $fields;

        return $this;
    }

    /** @return array<string> */
    public function getSearchableFields(): array
    {
        return $this->searchableFields;
    }

    public function pageSize(int $pageSize): self
    {
        $this->pageSize = $pageSize;

        return $this;
    }

    public function getPageSize(): int
    {
        return $this->pageSize;
    }
}
