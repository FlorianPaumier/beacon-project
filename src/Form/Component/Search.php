<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Form\Component;

use Devgeek\BeaconAdmin\Support\Component;

class Search extends Component
{
    protected string $name;

    protected string|\Closure|null $label = null;

    protected string|\Closure|null $placeholder = null;

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

    public function placeholder(string|\Closure|null $placeholder): static
    {
        $this->placeholder = $placeholder;

        return $this;
    }

    public function getPlaceholder(): ?string
    {
        return $this->evaluate($this->placeholder);
    }
}
