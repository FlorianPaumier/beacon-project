<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Twig\Component;

use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('BeaconAdmin:Repeater')]
class RepeaterComponent
{
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public string $name = '';

    #[LiveProp]
    public ?string $label = null;

    /** @var list<array<string, mixed>> */
    #[LiveProp(writable: true)]
    public array $items = [];

    /** @var array<array{name: string, type: string, label?: string}> */
    #[LiveProp]
    public array $schema = [];

    #[LiveProp]
    public ?int $minItems = null;

    #[LiveProp]
    public ?int $maxItems = null;

    #[LiveProp]
    public string $addLabel = 'Add';
}
