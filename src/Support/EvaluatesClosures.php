<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Support;

trait EvaluatesClosures
{
    public function evaluate(mixed $value): mixed
    {
        if ($value instanceof \Closure) {
            return $value();
        }

        return $value;
    }
}
