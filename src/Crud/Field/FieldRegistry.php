<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Crud\Field;

class FieldRegistry
{
    /** @var array<string, class-string<Field>> */
    protected array $mapping = [
        'string' => TextField::class,
        'text' => TextField::class,
        'integer' => NumberField::class,
        'float' => NumberField::class,
        'decimal' => NumberField::class,
        'boolean' => BooleanField::class,
        'datetime' => DateTimeField::class,
        'datetimetz' => DateTimeField::class,
        'date' => DateField::class,
        'time' => TimeField::class,
        'email' => EmailField::class,
    ];

    /**
     * @param class-string<Field> $fieldClass
     */
    public function register(string $doctrineType, string $fieldClass): void
    {
        $this->mapping[$doctrineType] = $fieldClass;
    }

    /**
     * @return class-string<Field>|null
     */
    public function getField(string $doctrineType): ?string
    {
        return $this->mapping[$doctrineType] ?? null;
    }

    /**
     * @return array<string, class-string<Field>>
     */
    public function getFields(): array
    {
        return $this->mapping;
    }
}
