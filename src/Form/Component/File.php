<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Form\Component;

use Devgeek\BeaconAdmin\Support\Component;

class File extends Component
{
    protected string $name;

    protected string|\Closure|null $label = null;

    protected bool|\Closure $required = false;

    protected bool|\Closure $multiple = false;

    protected string|\Closure|null $accept = null;

    protected int|\Closure|null $maxSize = null;

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

    public function multiple(bool|\Closure $multiple = true): static
    {
        $this->multiple = $multiple;

        return $this;
    }

    public function isMultiple(): bool
    {
        return (bool) $this->evaluate($this->multiple);
    }

    public function accept(string|\Closure|null $accept): static
    {
        $this->accept = $accept;

        return $this;
    }

    public function getAccept(): ?string
    {
        return $this->evaluate($this->accept);
    }

    public function maxSize(int|\Closure|null $maxSize): static
    {
        $this->maxSize = $maxSize;

        return $this;
    }

    public function getMaxSize(): ?int
    {
        return $this->evaluate($this->maxSize);
    }
}
