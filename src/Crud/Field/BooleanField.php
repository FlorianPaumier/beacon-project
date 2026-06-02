<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Crud\Field;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class BooleanField extends Field
{
    public function getFormType(): string
    {
        return CheckboxType::class;
    }
}
