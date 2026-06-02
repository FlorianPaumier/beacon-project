<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Form\Component;

use Devgeek\BeaconAdmin\Support\Component;

class Fieldset extends Component
{
    protected string|\Closure|null $label = null;

    /** @var array<Component> */
    protected array $schema = [];

    public function label(string|\Closure|null $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->evaluate($this->label);
    }

    /** @param array<Component> $components */
    public function schema(array $components): static
    {
        $this->schema = $components;

        return $this;
    }

    /** @return array<Component> */
    public function getSchema(): array
    {
        return $this->schema;
    }
}
