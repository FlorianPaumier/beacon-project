<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Support;

/** @phpstan-consistent-constructor */
abstract class Component
{
    public static function make(): static
    {
        return new static();
    }

    public function evaluate(mixed $value): mixed
    {
        if ($value instanceof \Closure) {
            return $value();
        }

        return $value;
    }
}
