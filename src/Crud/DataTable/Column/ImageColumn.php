<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Crud\DataTable\Column;

class ImageColumn extends Column
{
    protected ?string $template = 'image.html.twig';

    protected ?int $maxWidth = 80;

    protected ?int $maxHeight = 80;

    protected ?string $altField = null;

    protected bool $circular = false;

    public function maxWidth(?int $width): static
    {
        $this->maxWidth = $width;

        return $this;
    }

    public function getMaxWidth(): ?int
    {
        return $this->maxWidth;
    }

    public function maxHeight(?int $height): static
    {
        $this->maxHeight = $height;

        return $this;
    }

    public function getMaxHeight(): ?int
    {
        return $this->maxHeight;
    }

    public function altField(?string $field): static
    {
        $this->altField = $field;

        return $this;
    }

    public function getAltField(): ?string
    {
        return $this->altField;
    }

    public function circular(bool $circular = true): static
    {
        $this->circular = $circular;

        return $this;
    }

    public function isCircular(): bool
    {
        return $this->circular;
    }
}
