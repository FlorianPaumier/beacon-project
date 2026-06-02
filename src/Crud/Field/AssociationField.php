<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Crud\Field;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class AssociationField extends Field
{
    protected string $targetEntity;
    protected bool $multiple = false;

    public function targetEntity(string $targetEntity): static
    {
        $this->targetEntity = $targetEntity;

        return $this;
    }

    public function getTargetEntity(): string
    {
        return $this->targetEntity;
    }

    public function isMultiple(bool $multiple = true): static
    {
        $this->multiple = $multiple;

        return $this;
    }

    public function getIsMultiple(): bool
    {
        return $this->multiple;
    }

    public function getFormType(): string
    {
        return EntityType::class;
    }
}
