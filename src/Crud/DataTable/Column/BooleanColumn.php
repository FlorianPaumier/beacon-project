<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Crud\DataTable\Column;

class BooleanColumn extends Column
{
    protected ?string $template = 'boolean.html.twig';

    protected string $trueLabel = 'Yes';

    protected string $falseLabel = 'No';

    protected bool $useIcons = false;

    protected bool $toggleable = false;

    protected string $trueColor = 'success';

    protected string $falseColor = 'danger';

    public function trueLabel(string $label): static
    {
        $this->trueLabel = $label;

        return $this;
    }

    public function getTrueLabel(): string
    {
        return $this->trueLabel;
    }

    public function falseLabel(string $label): static
    {
        $this->falseLabel = $label;

        return $this;
    }

    public function getFalseLabel(): string
    {
        return $this->falseLabel;
    }

    public function useIcons(bool $useIcons = true): static
    {
        $this->useIcons = $useIcons;

        return $this;
    }

    public function isUsingIcons(): bool
    {
        return $this->useIcons;
    }

    public function trueColor(string $color): static
    {
        $this->trueColor = $color;

        return $this;
    }

    public function getTrueColor(): string
    {
        return $this->trueColor;
    }

    public function falseColor(string $color): static
    {
        $this->falseColor = $color;

        return $this;
    }

    public function getFalseColor(): string
    {
        return $this->falseColor;
    }

    public function toggleable(bool $toggleable = true): static
    {
        $this->toggleable = $toggleable;

        return $this;
    }

    public function isToggleable(): bool
    {
        return $this->toggleable;
    }
}
