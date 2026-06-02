<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Crud\DataTable\Column;

class TextColumn extends Column
{
    protected ?string $template = 'text.html.twig';

    protected ?int $limit = null;

    public function limit(?int $limit): static
    {
        $this->limit = $limit;

        return $this;
    }

    public function getLimit(): ?int
    {
        return $this->limit;
    }
}
