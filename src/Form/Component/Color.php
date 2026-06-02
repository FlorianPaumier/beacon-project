<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Form\Component;

use Devgeek\BeaconAdmin\Support\Component;

class Color extends Component
{
    protected string $name;

    protected string|\Closure|null $label = null;

    protected bool|\Closure $required = false;

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
}
