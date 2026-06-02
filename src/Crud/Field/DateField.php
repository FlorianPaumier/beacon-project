<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Crud\Field;

use Symfony\Component\Form\Extension\Core\Type\DateType;

class DateField extends Field
{
    public function getFormType(): string
    {
        return DateType::class;
    }
}
