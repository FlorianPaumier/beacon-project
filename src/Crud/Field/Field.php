<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Crud\Field;

/** @phpstan-consistent-constructor */
abstract class Field
{
    protected string $name;
    protected string $label;
    protected bool $required = false;
    protected ?string $permission = null;

    public static function make(string $name): static
    {
        $field = new static();
        $field->name = $name;
        $field->label = ucfirst(str_replace('_', ' ', $name));

        return $field;
    }

    public function label(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function required(bool $required = true): static
    {
        $this->required = $required;

        return $this;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function permission(?string $permission): static
    {
        $this->permission = $permission;

        return $this;
    }

    public function getPermission(): ?string
    {
        return $this->permission;
    }

    abstract public function getFormType(): string;
}
