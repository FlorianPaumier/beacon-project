<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Crud;

class CrudConfig
{
    protected string $entityClass;

    /** @var array<string> */
    protected array $fields = [];

    /** @var array<string> */
    protected array $sortableFields = [];

    /** @var array<string> */
    protected array $searchableFields = [];

    protected int $pageSize = 25;

    public static function make(): self
    {
        return new self();
    }

    public function entityClass(string $entityClass): self
    {
        $this->entityClass = $entityClass;

        return $this;
    }

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
