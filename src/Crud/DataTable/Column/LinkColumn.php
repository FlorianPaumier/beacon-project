<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Crud\DataTable\Column;

class LinkColumn extends Column
{
    protected ?string $template = 'link.html.twig';

    protected ?string $labelField = null;

    protected ?string $text = null;

    protected bool $openInNewTab = true;

    protected ?int $maxLength = null;

    public function labelField(?string $field): static
    {
        $this->labelField = $field;

        return $this;
    }

    public function getLabelField(): ?string
    {
        return $this->labelField;
    }

    public function text(?string $text): static
    {
        $this->text = $text;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function openInNewTab(bool $open = true): static
    {
        $this->openInNewTab = $open;

        return $this;
    }

    public function shouldOpenInNewTab(): bool
    {
        return $this->openInNewTab;
    }

    public function maxLength(?int $length): static
    {
        $this->maxLength = $length;

        return $this;
    }

    public function getMaxLength(): ?int
    {
        return $this->maxLength;
    }
}
