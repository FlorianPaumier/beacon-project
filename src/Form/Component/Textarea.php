<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Form\Component;

use Devgeek\BeaconAdmin\Support\Component;

class Textarea extends Component
{
    protected string $name;

    protected string|\Closure|null $label = null;

    protected bool|\Closure $required = false;

    protected string|\Closure|null $placeholder = null;

    protected int|\Closure|null $maxLength = null;

    protected int|\Closure $rows = 3;

    protected bool|\Closure $autoResize = false;

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

    public function maxLength(int|\Closure|null $length): static
    {
        $this->maxLength = $length;

        return $this;
    }

    public function getMaxLength(): ?int
    {
        return $this->evaluate($this->maxLength);
    }

    public function rows(int|\Closure $rows): static
    {
        $this->rows = $rows;

        return $this;
    }

    public function getRows(): int
    {
        return (int) $this->evaluate($this->rows);
    }

    public function autoResize(bool|\Closure $autoResize = true): static
    {
        $this->autoResize = $autoResize;

        return $this;
    }

    public function isAutoResize(): bool
    {
        return (bool) $this->evaluate($this->autoResize);
    }
}
