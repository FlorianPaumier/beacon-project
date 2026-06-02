<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Crud\Field;

use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class DateTimeField extends Field
{
    protected string $format = 'Y-m-d H:i';

    public function format(string $format): static
    {
        $this->format = $format;

        return $this;
    }

    public function getFormat(): string
    {
        return $this->format;
    }

    public function getFormType(): string
    {
        return DateTimeType::class;
    }
}
