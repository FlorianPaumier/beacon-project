<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Form\Validation;

class Rule
{
    protected string $rule;

    protected ?string $message = null;

    public static function make(string $rule): self
    {
        $self = new self();
        $self->rule = $rule;

        return $self;
    }

    public function rule(string $rule): static
    {
        $this->rule = $rule;

        return $this;
    }

    public function getRule(): string
    {
        return $this->rule;
    }

    public function message(string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }
}
