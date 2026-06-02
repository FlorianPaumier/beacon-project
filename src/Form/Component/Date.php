<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Form\Component;

use Devgeek\BeaconAdmin\Support\Component;

class Date extends Component
{
    protected string $name;

    protected string|\Closure|null $label = null;

    protected bool|\Closure $required = false;

    protected string|\Closure|null $min = null;

    protected string|\Closure|null $max = null;

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

    public function min(string|\Closure|null $min): static
    {
        $this->min = $min;

        return $this;
    }

    public function getMin(): ?string
    {
        return $this->evaluate($this->min);
    }

    public function max(string|\Closure|null $max): static
    {
        $this->max = $max;

        return $this;
    }

    public function getMax(): ?string
    {
        return $this->evaluate($this->max);
    }
}
