<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Crud\Doctrine;

class FieldMetadata
{
    protected string $name;
    protected string $type;
    protected bool $nullable = false;
    protected ?int $length = null;
    protected bool $unique = false;

    public static function make(): self
    {
        return new self();
    }

    public function name(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function type(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function nullable(bool $nullable): self
    {
        $this->nullable = $nullable;

        return $this;
    }

    public function isNullable(): bool
    {
        return $this->nullable;
    }

    public function length(?int $length): self
    {
        $this->length = $length;

        return $this;
    }

    public function getLength(): ?int
    {
        return $this->length;
    }

    public function unique(bool $unique): self
    {
        $this->unique = $unique;

        return $this;
    }

    public function isUnique(): bool
    {
        return $this->unique;
    }
}
