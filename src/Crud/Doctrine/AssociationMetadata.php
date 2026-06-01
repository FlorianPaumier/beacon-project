<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Crud\Doctrine;

class AssociationMetadata
{
    protected string $name;
    protected string $targetEntity;
    protected string $type; // 'ONE_TO_ONE', 'ONE_TO_MANY', 'MANY_TO_ONE', 'MANY_TO_MANY'
    protected bool $isOwningSide = true;

    public static function make(): self
    {
        return new self();
    }

    public function name(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function targetEntity(string $targetEntity): self
    {
        $this->targetEntity = $targetEntity;

        return $this;
    }

    public function getTargetEntity(): string
    {
        return $this->targetEntity;
    }

    public function type(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function isOwningSide(bool $isOwningSide): self
    {
        $this->isOwningSide = $isOwningSide;

        return $this;
    }

    public function getIsOwningSide(): bool
    {
        return $this->isOwningSide;
    }
}
