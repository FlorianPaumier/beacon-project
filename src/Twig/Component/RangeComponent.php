<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Twig\Component;

use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('BeaconAdmin:Range')]
class RangeComponent
{
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public string $name = '';

    #[LiveProp]
    public ?string $label = null;

    #[LiveProp(writable: true)]
    public mixed $value = 0;

    #[LiveProp]
    public float $min = 0;

    #[LiveProp]
    public float $max = 100;

    #[LiveProp]
    public float $step = 1;

    #[LiveProp]
    public bool $showValue = true;
}
