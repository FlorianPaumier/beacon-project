<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Crud\DataTable\Filter;

interface FilterInterface
{
    /** Returns the field name this filter operates on. */
    public function getField(): string;

    /** Returns the filter type identifier (boolean, choice, date, entity, text). */
    public function getType(): string;

    /**
     * Returns the operator (eq, neq, gt, gte, lt, lte, in, between, isNull, isNotNull, like).
     * Used when the filter value is applied via the generic operator pipeline.
     */
    public function getOperator(): string;

    /** Returns any extra metadata needed for rendering (choices, target entity class, etc.). */
    /** @return array<string, mixed> */
    public function getMeta(): array;
}
