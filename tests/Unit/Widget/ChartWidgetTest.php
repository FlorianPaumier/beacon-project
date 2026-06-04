<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Widget;

use Devgeek\BeaconAdmin\Widget\ChartWidget;
use Devgeek\BeaconAdmin\Widget\ChartWidgetData;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Twig\Environment;

final class ChartWidgetTest extends TestCase
{
    #[Test]
    public function itCreatesViaMake(): void
    {
        $twig = $this->createMock(Environment::class);
        $widget = ChartWidget::make($twig);

        $this->assertSame(6, $widget->getCols());
    }

    #[Test]
    public function itSetsNameAndLabelFluently(): void
    {
        $widget = ChartWidget::make($this->createMock(Environment::class))
            ->name('sales')
            ->label('Monthly Sales');

        $this->assertSame('sales', $widget->getName());
        $this->assertSame('Monthly Sales', $widget->getLabel());
    }

    #[Test]
    public function itHasDefaultColsOfSix(): void
    {
        $widget = ChartWidget::make($this->createMock(Environment::class))
            ->name('sales')
            ->label('Sales');

        $this->assertSame(6, $widget->getCols());
    }

    #[Test]
    public function itHasDefaultPriorityOfZero(): void
    {
        $widget = ChartWidget::make($this->createMock(Environment::class))
            ->name('sales')
            ->label('Sales');

        $this->assertSame(0, $widget->getPriority());
    }

    #[Test]
    public function itReturnsLiteralChartData(): void
    {
        $data = ChartWidgetData::make('line')
            ->withLabels(['Jan', 'Feb', 'Mar'])
            ->withDatasets([
                ['label' => 'Revenue', 'data' => [100, 200, 150]],
            ]);

        $widget = ChartWidget::make($this->createMock(Environment::class))
            ->name('sales')
            ->label('Sales')
            ->data($data);

        $result = $widget->getChartData();

        $this->assertSame('line', $result->type);
        $this->assertSame(['Jan', 'Feb', 'Mar'], $result->labels);
    }

    #[Test]
    public function itResolvesClosureData(): void
    {
        $expected = ChartWidgetData::make('bar')
            ->withLabels(['A', 'B'])
            ->withDatasets([['label' => 'Count', 'data' => [5, 10]]]);

        $widget = ChartWidget::make($this->createMock(Environment::class))
            ->name('sales')
            ->label('Sales')
            ->data(fn () => $expected);

        $result = $widget->getChartData();

        $this->assertSame('bar', $result->type);
    }

    #[Test]
    public function itRendersViaTwig(): void
    {
        $chartData = ChartWidgetData::make('line')
            ->withLabels(['Q1', 'Q2'])
            ->withDatasets([['label' => 'Sales', 'data' => [50, 75]]]);

        $twig = $this->createMock(Environment::class);
        $twig->expects($this->once())
            ->method('render')
            ->with(
                '@BeaconAdmin/widgets/chart.html.twig',
                [
                    'name' => 'sales',
                    'chartConfig' => $chartData->toJson(),
                ],
            )
            ->willReturn('<canvas>rendered</canvas>');

        $widget = ChartWidget::make($twig)
            ->name('sales')
            ->label('Sales')
            ->data($chartData);

        $result = $widget->render();

        $this->assertStringContainsString('rendered', $result);
    }

    #[Test]
    public function itCustomizesCols(): void
    {
        $widget = ChartWidget::make($this->createMock(Environment::class))
            ->name('chart')
            ->label('Chart')
            ->cols(12);

        $this->assertSame(12, $widget->getCols());
    }

    #[Test]
    public function itCustomizesPriority(): void
    {
        $widget = ChartWidget::make($this->createMock(Environment::class))
            ->name('chart')
            ->label('Chart')
            ->priority(10);

        $this->assertSame(10, $widget->getPriority());
    }
}
