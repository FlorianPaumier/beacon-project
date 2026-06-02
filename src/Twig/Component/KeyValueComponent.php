<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Twig\Component;

use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('BeaconAdmin:KeyValue')]
class KeyValueComponent
{
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public string $name = '';

    #[LiveProp]
    public ?string $label = null;

    #[LiveProp(writable: true)]
    public array $value = [];

    #[LiveProp]
    public bool $required = false;

    #[LiveProp]
    public ?string $keyPlaceholder = null;

    #[LiveProp]
    public ?string $valuePlaceholder = null;

    #[LiveProp]
    public bool $allowDelete = true;

    #[LiveProp]
    public bool $allowAdd = true;
}
