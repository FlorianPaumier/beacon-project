<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Crud\DataTable\Column;

/** @phpstan-consistent-constructor */
class ActionsColumn extends Column
{
    protected string $editRoute = '';
    protected string $deleteRoute = '';
    protected ?string $template = 'actions.html.twig';

    public static function make(string $name = '__actions'): static
    {
        $column = new static();
        $column->name = $name;
        $column->label = 'Actions';

        return $column;
    }

    public function editRoute(string $route): static
    {
        $this->editRoute = $route;

        return $this;
    }

    public function getEditRoute(): string
    {
        return $this->editRoute;
    }

    public function deleteRoute(string $route): static
    {
        $this->deleteRoute = $route;

        return $this;
    }

    public function getDeleteRoute(): string
    {
        return $this->deleteRoute;
    }
}
