<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Widget;

use Devgeek\BeaconAdmin\Support\EvaluatesClosures;
use Twig\Environment;

class StatsWidget implements DashboardWidgetInterface
{
    use EvaluatesClosures;
    protected string $name;
    protected string $label;
    protected int|float|string|\Closure $value;
    protected ?string $icon = null;
    protected float|\Closure|null $trend = null;
    protected ?string $trendLabel = null;
    protected int $cols = 3;
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

    public function value(int|float|string|\Closure $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function icon(?string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function trend(float|\Closure|null $trend): static
    {
        $this->trend = $trend;

        return $this;
    }

    public function trendLabel(?string $trendLabel): static
    {
        $this->trendLabel = $trendLabel;

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

    public function getValue(): int|float|string
    {
        return $this->evaluate($this->value);
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function getTrend(): ?float
    {
        return $this->evaluate($this->trend);
    }

    public function getTrendLabel(): ?string
    {
        return $this->trendLabel;
    }

    public function getCols(): int
    {
        return $this->cols;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function render(): string
    {
        return $this->twig->render('@BeaconAdmin/widgets/stats.html.twig', [
            'label' => $this->getLabel(),
            'value' => $this->getValue(),
            'icon' => $this->getIcon(),
            'trend' => $this->getTrend(),
            'trendLabel' => $this->getTrendLabel(),
        ]);
    }

    public function getType(): string
    {
        return 'stats';
    }
}
