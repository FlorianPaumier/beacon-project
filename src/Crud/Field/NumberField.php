<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Crud\Field;

use Symfony\Component\Form\Extension\Core\Type\NumberType;

class NumberField extends Field
{
    protected ?float $minValue = null;
    protected ?float $maxValue = null;
    protected ?float $stepValue = null;

    public function min(?float $min): static
    {
        $this->minValue = $min;

        return $this;
    }

    public function getMin(): ?float
    {
        return $this->minValue;
    }

    public function max(?float $max): static
    {
        $this->maxValue = $max;

        return $this;
    }

    public function getMax(): ?float
    {
        return $this->maxValue;
    }

    public function step(?float $step): static
    {
        $this->stepValue = $step;

        return $this;
    }

    public function getStep(): ?float
    {
        return $this->stepValue;
    }

    public function getFormType(): string
    {
        return NumberType::class;
    }
}
