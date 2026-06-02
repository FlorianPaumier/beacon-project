<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Form\Component;

use Devgeek\BeaconAdmin\Support\Component;

class Number extends Component
{
    protected string $name;

    protected string|\Closure|null $label = null;

    protected bool|\Closure $required = false;

    protected string|\Closure|null $placeholder = null;

    protected float|\Closure|null $min = null;

    protected float|\Closure|null $max = null;

    protected float|\Closure|null $step = null;

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

    public function placeholder(string|\Closure|null $placeholder): static
    {
        $this->placeholder = $placeholder;

        return $this;
    }

    public function getPlaceholder(): ?string
    {
        return $this->evaluate($this->placeholder);
    }

    public function min(float|\Closure|null $min): static
    {
        $this->min = $min;

        return $this;
    }

    public function getMin(): ?float
    {
        return $this->evaluate($this->min);
    }

    public function max(float|\Closure|null $max): static
    {
        $this->max = $max;

        return $this;
    }

    public function getMax(): ?float
    {
        return $this->evaluate($this->max);
    }

    public function step(float|\Closure|null $step): static
    {
        $this->step = $step;

        return $this;
    }

    public function getStep(): ?float
    {
        return $this->evaluate($this->step);
    }
}
