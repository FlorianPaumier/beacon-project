<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Form\Component;

use Devgeek\BeaconAdmin\Support\Component;

class Repeater extends Component
{
    protected string $name;

    protected string|\Closure|null $label = null;

    /** @var array<Component> */
    protected array $schema = [];

    protected int|\Closure|null $minItems = null;

    protected int|\Closure|null $maxItems = null;

    protected string|\Closure $addLabel = 'Add';

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

    public function minItems(int|\Closure|null $minItems): static
    {
        $this->minItems = $minItems;

        return $this;
    }

    public function getMinItems(): ?int
    {
        return $this->evaluate($this->minItems);
    }

    public function maxItems(int|\Closure|null $maxItems): static
    {
        $this->maxItems = $maxItems;

        return $this;
    }

    public function getMaxItems(): ?int
    {
        return $this->evaluate($this->maxItems);
    }

    public function addLabel(string|\Closure $addLabel): static
    {
        $this->addLabel = $addLabel;

        return $this;
    }

    public function getAddLabel(): string
    {
        return (string) $this->evaluate($this->addLabel);
    }
}
