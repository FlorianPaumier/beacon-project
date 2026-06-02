<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Twig\Component;

use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('BeaconAdmin:Fieldset')]
class FieldsetComponent
{
    use DefaultActionTrait;

    #[LiveProp]
    public ?string $label = null;

    /** @var array<int, string> */
    #[LiveProp]
    public array $schema = [];
}
