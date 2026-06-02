<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Twig\Component;

use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('BeaconAdmin:Select')]
class SelectComponent
{
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public string $name = '';

    #[LiveProp]
    public ?string $label = null;

    #[LiveProp]
    public bool $required = false;

    #[LiveProp(writable: true)]
    public mixed $value = '';

    /** @var array<string, string> */
    #[LiveProp]
    public array $options = [];

    #[LiveProp]
    public bool $multiple = false;

    #[LiveProp]
    public bool $searchable = false;
}
