<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Crud\Doctrine;

class EntityMetadata
{
    protected string $className;
    protected string $tableName;

    /** @var array<FieldMetadata> */
    protected array $fields = [];

    /** @var array<FieldMetadata> */
    protected array $identifier = [];

    /** @var array<AssociationMetadata> */
    protected array $associations = [];

    public static function make(): self
    {
        return new self();
    }

    public function className(string $className): self
    {
        $this->className = $className;

        return $this;
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    public function tableName(string $tableName): self
    {
        $this->tableName = $tableName;

        return $this;
    }

    public function getTableName(): string
    {
        return $this->tableName;
    }

    /** @param array<FieldMetadata> $fields */
    public function fields(array $fields): self
    {
        $this->fields = $fields;

        return $this;
    }

    /** @return array<FieldMetadata> */
    public function getFields(): array
    {
        return $this->fields;
    }

    /** @param array<FieldMetadata> $identifier */
    public function identifier(array $identifier): self
    {
        $this->identifier = $identifier;

        return $this;
    }

    /** @return array<FieldMetadata> */
    public function getIdentifier(): array
    {
        return $this->identifier;
    }

    /** @param array<AssociationMetadata> $associations */
    public function associations(array $associations): self
    {
        $this->associations = $associations;

        return $this;
    }

    /** @return array<AssociationMetadata> */
    public function getAssociations(): array
    {
        return $this->associations;
    }

    /** @return array<string> */
    public function getFieldNames(): array
    {
        return array_map(static fn (FieldMetadata $f): string => $f->getName(), $this->fields);
    }
}
