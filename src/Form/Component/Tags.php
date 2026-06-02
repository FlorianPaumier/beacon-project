<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Form\Component;

use Devgeek\BeaconAdmin\Support\Component;

class Tags extends Component
{
    protected string $name;

    protected string|\Closure|null $label = null;

    protected bool|\Closure $required = false;

    protected string|\Closure|null $placeholder = null;

    /** @var array<string>|\Closure */
    protected array|\Closure $suggestions = [];

    protected int|\Closure|null $maxTags = null;

    protected bool|\Closure $allowCustom = true;

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

    /** @param array<string>|\Closure $suggestions */
    public function suggestions(array|\Closure $suggestions): static
    {
        $this->suggestions = $suggestions;

        return $this;
    }

    /** @return array<string> */
    public function getSuggestions(): array
    {
        return $this->evaluate($this->suggestions);
    }

    public function maxTags(int|\Closure|null $maxTags): static
    {
        $this->maxTags = $maxTags;

        return $this;
    }

    public function getMaxTags(): ?int
    {
        return $this->evaluate($this->maxTags);
    }

    public function allowCustom(bool|\Closure $allowCustom = true): static
    {
        $this->allowCustom = $allowCustom;

        return $this;
    }

    public function isAllowCustom(): bool
    {
        return (bool) $this->evaluate($this->allowCustom);
    }
}
