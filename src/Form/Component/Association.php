<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Form\Component;

use Devgeek\BeaconAdmin\Support\Component;

class Association extends Component
{
    protected string $name;

    protected string|\Closure|null $label = null;

    protected bool|\Closure $required = false;

    protected string|\Closure|null $targetEntity = null;

    protected bool|\Closure $multiple = false;

    protected bool|\Closure $searchable = false;

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

    public function required(bool|\Closure $required = true): static
    {
        $this->required = $required;

        return $this;
    }

    public function isRequired(): bool
    {
        return (bool) $this->evaluate($this->required);
    }

    public function targetEntity(string|\Closure|null $targetEntity): static
    {
        $this->targetEntity = $targetEntity;

        return $this;
    }

    public function getTargetEntity(): ?string
    {
        return $this->evaluate($this->targetEntity);
    }

    public function multiple(bool|\Closure $multiple = true): static
    {
        $this->multiple = $multiple;

        return $this;
    }

    public function isMultiple(): bool
    {
        return (bool) $this->evaluate($this->multiple);
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
}
