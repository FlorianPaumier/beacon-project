<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Crud\DataTable\Column;

class NumberColumn extends Column
{
    protected ?string $template = 'number.html.twig';

    protected int $decimals = 0;
    protected string $decimalSeparator = '.';
    protected string $thousandsSeparator = ',';

    public function decimals(int $decimals): static
    {
        $this->decimals = $decimals;

        return $this;
    }

    public function getDecimals(): int
    {
        return $this->decimals;
    }

    public function decimalSeparator(string $separator): static
    {
        $this->decimalSeparator = $separator;

        return $this;
    }

    public function getDecimalSeparator(): string
    {
        return $this->decimalSeparator;
    }

    public function thousandsSeparator(string $separator): static
    {
        $this->thousandsSeparator = $separator;

        return $this;
    }

    public function getThousandsSeparator(): string
    {
        return $this->thousandsSeparator;
    }

    public function formatValue(mixed $value): string
    {
        if ($value === null) {
            return '';
        }

        return number_format(
            (float) $value,
            $this->decimals,
            $this->decimalSeparator,
            $this->thousandsSeparator,
        );
    }
}
