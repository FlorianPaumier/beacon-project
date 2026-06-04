<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Widget;

use Devgeek\BeaconAdmin\Support\EvaluatesClosures;
use Twig\Environment;

class ChartWidget implements DashboardWidgetInterface
{
    use EvaluatesClosures;

    protected string $name;
    protected string $label;
    protected ChartWidgetData|\Closure $data;
    protected int $cols = 6;
    protected int $priority = 0;
    protected Environment $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public static function make(Environment $twig): self
    {
        return new self($twig);
    }

    public function name(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function label(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function data(ChartWidgetData|\Closure $data): static
    {
        $this->data = $data;

        return $this;
    }

    public function cols(int $cols): static
    {
        $this->cols = $cols;

        return $this;
    }

    public function priority(int $priority): static
    {
        $this->priority = $priority;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getCols(): int
    {
        return $this->cols;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function getChartData(): ChartWidgetData
    {
        $data = $this->evaluate($this->data);

        if ($data instanceof \Closure) {
            $data = ($data)();
        }

        return $data;
    }

    public function render(): string
    {
        $chartData = $this->getChartData();

        return $this->twig->render('@BeaconAdmin/widgets/chart.html.twig', [
            'name' => $this->getName(),
            'chartConfig' => $chartData->toJson(),
        ]);
    }
}
