<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Twig\Component;

use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('BeaconAdmin:Number')]
class NumberComponent
{
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public string $name = '';

    #[LiveProp]
    public ?string $label = null;

    #[LiveProp(writable: true)]
    public mixed $value = null;

    #[LiveProp]
    public bool $required = false;

    #[LiveProp]
    public ?string $placeholder = null;

    #[LiveProp]
    public ?float $min = null;

    #[LiveProp]
    public ?float $max = null;

    #[LiveProp]
    public ?float $step = null;
}
