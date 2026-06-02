<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Crud\DataTable\Column;

class DateTimeColumn extends Column
{
    protected string $format = 'Y-m-d H:i';
    protected ?string $template = 'date.html.twig';

    public function format(string $format): static
    {
        $this->format = $format;

        return $this;
    }

    public function getFormat(): string
    {
        return $this->format;
    }
}
