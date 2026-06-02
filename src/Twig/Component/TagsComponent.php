<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Twig\Component;

use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('BeaconAdmin:Tags')]
class TagsComponent
{
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public string $name = '';

    #[LiveProp]
    public ?string $label = null;

    /** @var string[] */
    #[LiveProp(writable: true)]
    public array $value = [];

    #[LiveProp]
    public bool $required = false;

    #[LiveProp]
    public ?string $placeholder = null;

    /** @var array<string> */
    #[LiveProp]
    public array $suggestions = [];

    #[LiveProp]
    public ?int $maxTags = null;

    #[LiveProp]
    public bool $allowCustom = true;
}
