<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Crud\Field;

use Symfony\Component\Form\Extension\Core\Type\EmailType;

class EmailField extends Field
{
    public function getFormType(): string
    {
        return EmailType::class;
    }
}
