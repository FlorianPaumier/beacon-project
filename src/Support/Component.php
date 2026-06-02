<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Support;

/** @phpstan-consistent-constructor */
abstract class Component
{
    use EvaluatesClosures;

    public static function make(): static
    {
        return new static();
    }
}
