<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Widget;

use Devgeek\BeaconAdmin\Widget\ChartWidgetData;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ChartWidgetDataTest extends TestCase
{
    #[Test]
    public function itCreatesWithDefaults(): void
    {
        $data = ChartWidgetData::make();

        $this->assertSame('line', $data->type);
        $this->assertSame([], $data->labels);
        $this->assertSame([], $data->datasets);
        $this->assertSame([], $data->options);
    }

    #[Test]
    public function itCreatesWithCustomType(): void
    {
        $data = ChartWidgetData::make('bar');

        $this->assertSame('bar', $data->type);
    }

    #[Test]
    public function itIsImmutableWithLabels(): void
    {
        $original = ChartWidgetData::make();
        $updated = $original->withLabels(['A', 'B', 'C']);

        $this->assertSame([], $original->labels);
        $this->assertSame(['A', 'B', 'C'], $updated->labels);
    }

    #[Test]
    public function itIsImmutableWithDatasets(): void
    {
        $datasets = [
            ['label' => 'Revenue', 'data' => [100, 200]],
            ['label' => 'Cost', 'data' => [50, 80]],
        ];
        $original = ChartWidgetData::make();
        $updated = $original->withDatasets($datasets);

        $this->assertSame([], $original->datasets);
        $this->assertSame($datasets, $updated->datasets);
    }

    #[Test]
    public function itIsImmutableWithOptions(): void
    {
        $options = ['responsive' => true, 'maintainAspectRatio' => false];
        $original = ChartWidgetData::make();
        $updated = $original->withOptions($options);

        $this->assertSame([], $original->options);
        $this->assertSame($options, $updated->options);
    }

    #[Test]
    public function itProducesValidChartJsConfig(): void
    {
        $data = ChartWidgetData::make('pie')
            ->withLabels(['Red', 'Blue', 'Yellow'])
            ->withDatasets([
                ['label' => 'Votes', 'data' => [12, 19, 3]],
            ])
            ->withOptions(['responsive' => true]);

        $config = $data->toArray();

        $this->assertSame('pie', $config['type']);
        $this->assertArrayHasKey('data', $config);
        $this->assertArrayHasKey('labels', $config['data']);
        $this->assertArrayHasKey('datasets', $config['data']);
        $this->assertArrayHasKey('options', $config);
    }

    #[Test]
    public function itProducesValidJson(): void
    {
        $data = ChartWidgetData::make('line')
            ->withLabels(['Jan', 'Feb'])
            ->withDatasets([['label' => 'Users', 'data' => [10, 20]]]);

        $json = $data->toJson();

        $this->assertJson($json);
        $decoded = json_decode($json, true, flags: JSON_THROW_ON_ERROR);
        $this->assertSame('line', $decoded['type']);
    }

    #[Test]
    public function itSupportsAllChartTypes(): void
    {
        $types = ['line', 'bar', 'pie', 'doughnut', 'radar'];

        foreach ($types as $type) {
            $data = ChartWidgetData::make($type);
            $this->assertSame($type, $data->type);
        }
    }
}
