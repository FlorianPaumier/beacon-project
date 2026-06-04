<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Crud\DataTable\Filter;

class BooleanFilter implements FilterInterface
{
    public function __construct(
        private readonly string $field,
    ) {
    }

    public static function make(string $field): self
    {
        return new self($field);
    }

    public function getField(): string
    {
        return $this->field;
    }

    public function getType(): string
    {
        return 'boolean';
    }

    public function getOperator(): string
    {
        return 'eq';
    }

    /** @return array<string, mixed> */
    public function getMeta(): array
    {
        return [
            'type' => $this->getType(),
            'operator' => $this->getOperator(),
        ];
    }
}
