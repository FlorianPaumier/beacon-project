<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Crud\DataTable\Filter;

class EntityFilter implements FilterInterface
{
    /** @param class-string $targetEntity */
    public function __construct(
        private readonly string $field,
        private readonly string $targetEntity,
        private readonly ?string $labelField = null,
    ) {
    }

    /** @param class-string $targetEntity */
    public static function make(string $field, string $targetEntity, ?string $labelField = null): self
    {
        return new self($field, $targetEntity, $labelField);
    }

    public function getField(): string
    {
        return $this->field;
    }

    public function getType(): string
    {
        return 'entity';
    }

    public function getOperator(): string
    {
        return 'eq';
    }

    /** @return class-string */
    public function getTargetEntity(): string
    {
        return $this->targetEntity;
    }

    public function getLabelField(): ?string
    {
        return $this->labelField;
    }

    /** @return array<string, mixed> */
    public function getMeta(): array
    {
        $meta = [
            'type' => $this->getType(),
            'operator' => $this->getOperator(),
            'class' => $this->targetEntity,
        ];

        if ($this->labelField !== null) {
            $meta['label'] = $this->labelField;
        }

        return $meta;
    }
}
