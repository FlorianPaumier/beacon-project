<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Widget;

readonly class ChartWidgetData
{
    public string $type;

    /** @var array<string> */
    public array $labels;

    /** @var array<int, array{label: string, data: array<int|float>, backgroundColor?: string|array<string>, borderColor?: string|array<string>}> */
    public array $datasets;

    /** @var array<string, mixed> */
    public array $options;

    /**
     * @param array<string>                                                                                                                        $labels
     * @param array<int, array{label: string, data: array<int|float>, backgroundColor?: string|array<string>, borderColor?: string|array<string>}> $datasets
     * @param array<string, mixed>                                                                                                                 $options
     */
    public function __construct(
        string $type = 'line',
        array $labels = [],
        array $datasets = [],
        array $options = [],
    ) {
        $this->type = $type;
        $this->labels = $labels;
        $this->datasets = $datasets;
        $this->options = $options;
    }

    public static function make(string $type = 'line'): self
    {
        return new self($type);
    }

    /** @param array<string> $labels */
    public function withLabels(array $labels): self
    {
        return new self($this->type, $labels, $this->datasets, $this->options);
    }

    /** @param array<int, array{label: string, data: array<int|float>, backgroundColor?: string|array<string>, borderColor?: string|array<string>}> $datasets */
    public function withDatasets(array $datasets): self
    {
        return new self($this->type, $this->labels, $datasets, $this->options);
    }

    /** @param array<string, mixed> $options */
    public function withOptions(array $options): self
    {
        return new self($this->type, $this->labels, $this->datasets, $options);
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'data' => [
                'labels' => $this->labels,
                'datasets' => $this->datasets,
            ],
            'options' => $this->options,
        ];
    }

    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_THROW_ON_ERROR);
    }
}
