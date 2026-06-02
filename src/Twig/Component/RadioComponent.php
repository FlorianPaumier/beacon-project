<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Twig\Component;

use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('BeaconAdmin:Radio')]
class RadioComponent
{
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public string $name = '';

    #[LiveProp]
    public ?string $label = null;

    #[LiveProp(writable: true)]
    public mixed $value = '';

    #[LiveProp]
    public bool $required = false;

    /** @var array<string, string> */
    #[LiveProp]
    public array $options = [];

    #[LiveProp]
    public string $layout = 'vertical';
}
