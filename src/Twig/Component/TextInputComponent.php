<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Twig\Component;

use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('BeaconAdmin:TextInput')]
class TextInputComponent
{
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public string $name = '';

    #[LiveProp]
    public ?string $label = null;

    #[LiveProp]
    public bool $required = false;

    #[LiveProp]
    public bool $email = false;

    #[LiveProp]
    public ?int $maxLength = null;

    #[LiveProp]
    public ?string $placeholder = null;

    #[LiveProp(writable: true)]
    public mixed $value = '';
}
