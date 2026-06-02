<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Table\Column;

use Devgeek\BeaconAdmin\Support\Component;

class TextColumn extends Component
{
    protected string $name;

    protected string|\Closure|null $label = null;

    protected bool|\Closure $sortable = false;

    protected bool|\Closure $searchable = false;

    protected ?\Closure $format = null;

    protected int|\Closure|null $limit = null;

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

    public function sortable(bool|\Closure $sortable = true): static
    {
        $this->sortable = $sortable;

        return $this;
    }

    public function isSortable(): bool
    {
        return (bool) $this->evaluate($this->sortable);
    }

    public function searchable(bool|\Closure $searchable = true): static
    {
        $this->searchable = $searchable;

        return $this;
    }

    public function isSearchable(): bool
    {
        return (bool) $this->evaluate($this->searchable);
    }

    public function format(?\Closure $formatter): static
    {
        $this->format = $formatter;

        return $this;
    }

    public function getFormat(): ?\Closure
    {
        return $this->format;
    }

    public function limit(int|\Closure|null $limit): static
    {
        $this->limit = $limit;

        return $this;
    }

    public function getLimit(): ?int
    {
        return $this->evaluate($this->limit);
    }
}
