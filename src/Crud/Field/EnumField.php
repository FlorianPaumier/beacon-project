<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Crud\Field;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class EnumField extends Field
{
    /** @var array<string, string> */
    protected array $options = [];

    protected ?string $enumClass = null;

    /** @param array<string, string> $options */
    public function options(array $options): static
    {
        $this->options = $options;

        return $this;
    }

    /** @return array<string, string> */
    public function getOptions(): array
    {
        return $this->options;
    }

    public function enumClass(?string $enumClass): static
    {
        $this->enumClass = $enumClass;

        return $this;
    }

    public function getEnumClass(): ?string
    {
        return $this->enumClass;
    }

    public function getFormType(): string
    {
        return ChoiceType::class;
    }
}
