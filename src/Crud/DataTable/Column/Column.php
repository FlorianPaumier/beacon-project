<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Crud\DataTable\Column;

/** @phpstan-consistent-constructor */
abstract class Column
{
    protected string $name;
    protected string $label;
    protected bool $sortable = false;
    protected ?string $template = null;

    public static function make(string $name): static
    {
        $column = new static();
        $column->name = $name;
        $column->label = ucfirst(str_replace('_', ' ', $name));

        return $column;
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

    public function sortable(bool $sortable = true): static
    {
        $this->sortable = $sortable;

        return $this;
    }

    public function isSortable(): bool
    {
        return $this->sortable;
    }

    public function getTemplate(): string
    {
        if ($this->template !== null) {
            return $this->template;
        }

        $class = static::class;
        $shortName = substr(strrchr($class, '\\'), 1);

        return lcfirst($shortName).'.html.twig';
    }
}
