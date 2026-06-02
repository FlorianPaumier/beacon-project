<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Table\Filter;

use Devgeek\BeaconAdmin\Support\Component;
use Doctrine\ORM\QueryBuilder;

abstract class Filter extends Component
{
    protected string $name;

    protected string|\Closure|null $label = null;

    public function name(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function label(string|\Closure|null $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->evaluate($this->label);
    }

    abstract public function apply(QueryBuilder $queryBuilder, mixed $value): void;
}
