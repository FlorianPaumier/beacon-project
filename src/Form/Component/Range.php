<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Form\Component;

use Devgeek\BeaconAdmin\Support\Component;

class Range extends Component
{
    protected string $name;

    protected string|\Closure|null $label = null;

    protected float|\Closure $min = 0;

    protected float|\Closure $max = 100;

    protected float|\Closure $step = 1;

    protected bool|\Closure $showValue = true;

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

    public function min(float|\Closure $min): static
    {
        $this->min = $min;

        return $this;
    }

    public function getMin(): float
    {
        return (float) $this->evaluate($this->min);
    }

    public function max(float|\Closure $max): static
    {
        $this->max = $max;

        return $this;
    }

    public function getMax(): float
    {
        return (float) $this->evaluate($this->max);
    }

    public function step(float|\Closure $step): static
    {
        $this->step = $step;

        return $this;
    }

    public function getStep(): float
    {
        return (float) $this->evaluate($this->step);
    }

    public function showValue(bool|\Closure $showValue = true): static
    {
        $this->showValue = $showValue;

        return $this;
    }

    public function isShowValue(): bool
    {
        return (bool) $this->evaluate($this->showValue);
    }
}
