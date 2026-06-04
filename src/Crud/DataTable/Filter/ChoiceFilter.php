<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Crud\DataTable\Filter;

class ChoiceFilter implements FilterInterface
{
    /** @param array<string, string> $choices */
    public function __construct(
        private readonly string $field,
        private readonly array $choices,
        private readonly string $operator = 'eq',
    ) {
    }

    /** @param array<string, string> $choices */
    public static function make(string $field, array $choices, string $operator = 'eq'): self
    {
        return new self($field, $choices, $operator);
    }

    public function getField(): string
    {
        return $this->field;
    }

    public function getType(): string
    {
        return 'choice';
    }

    public function getOperator(): string
    {
        return $this->operator;
    }

    /** @return array<string, string> */
    public function getChoices(): array
    {
        return $this->choices;
    }

    /** @return array<string, mixed> */
    public function getMeta(): array
    {
        return [
            'type' => $this->getType(),
            'operator' => $this->getOperator(),
            'choices' => $this->choices,
        ];
    }
}
