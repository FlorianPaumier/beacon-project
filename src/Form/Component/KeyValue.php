<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Form\Component;

use Devgeek\BeaconAdmin\Support\Component;

class KeyValue extends Component
{
    protected string $name;

    protected string|\Closure|null $label = null;

    protected bool|\Closure $required = false;

    protected string|\Closure|null $keyPlaceholder = null;

    protected string|\Closure|null $valuePlaceholder = null;

    protected bool|\Closure $allowDelete = true;

    protected bool|\Closure $allowAdd = true;

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

    public function keyPlaceholder(string|\Closure|null $keyPlaceholder): static
    {
        $this->keyPlaceholder = $keyPlaceholder;

        return $this;
    }

    public function getKeyPlaceholder(): ?string
    {
        return $this->evaluate($this->keyPlaceholder);
    }

    public function valuePlaceholder(string|\Closure|null $valuePlaceholder): static
    {
        $this->valuePlaceholder = $valuePlaceholder;

        return $this;
    }

    public function getValuePlaceholder(): ?string
    {
        return $this->evaluate($this->valuePlaceholder);
    }

    public function allowDelete(bool|\Closure $allowDelete = true): static
    {
        $this->allowDelete = $allowDelete;

        return $this;
    }

    public function isAllowDelete(): bool
    {
        return (bool) $this->evaluate($this->allowDelete);
    }

    public function allowAdd(bool|\Closure $allowAdd = true): static
    {
        $this->allowAdd = $allowAdd;

        return $this;
    }

    public function isAllowAdd(): bool
    {
        return (bool) $this->evaluate($this->allowAdd);
    }
}
