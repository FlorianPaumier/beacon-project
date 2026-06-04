<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Crud\DataTable\Filter;

class DateFilter implements FilterInterface
{
    public function __construct(
        private readonly string $field,
        private readonly string $operator = 'between',
    ) {
    }

    public static function make(string $field, string $operator = 'between'): self
    {
        return new self($field, $operator);
    }

    public function getField(): string
    {
        return $this->field;
    }

    public function getType(): string
    {
        return 'datetime';
    }

    public function getOperator(): string
    {
        return $this->operator;
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
