<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Widget;

use Devgeek\BeaconAdmin\Widget\StatsWidget;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Twig\Environment;

final class StatsWidgetTest extends TestCase
{
    #[Test]
    public function itCreatesViaMake(): void
    {
        $twig = $this->createMock(Environment::class);
        $widget = StatsWidget::make($twig);

        $this->assertSame(3, $widget->getCols());
    }

    #[Test]
    public function itSetsNameAndLabelFluently(): void
    {
        $widget = StatsWidget::make($this->createMock(Environment::class))
            ->name('users')
            ->label('Total Users');

        $this->assertSame('users', $widget->getName());
        $this->assertSame('Total Users', $widget->getLabel());
    }

    #[Test]
    public function itReturnsLiteralValue(): void
    {
        $widget = StatsWidget::make($this->createMock(Environment::class))
            ->name('users')
            ->label('Users')
            ->value(42);

        $this->assertSame(42, $widget->getValue());
    }

    #[Test]
    public function itResolvesClosureValue(): void
    {
        $widget = StatsWidget::make($this->createMock(Environment::class))
            ->name('users')
            ->label('Users')
            ->value(fn () => 99);

        $this->assertSame(99, $widget->getValue());
    }

    #[Test]
    public function itReturnsNullTrendByDefault(): void
    {
        $widget = StatsWidget::make($this->createMock(Environment::class))
            ->name('users')
            ->label('Users')
            ->value(10);

        $this->assertNull($widget->getTrend());
    }

    #[Test]
    public function itResolvesClosureTrend(): void
    {
        $widget = StatsWidget::make($this->createMock(Environment::class))
            ->name('users')
            ->label('Users')
            ->value(10)
            ->trend(fn () => 5.5);

        $this->assertSame(5.5, $widget->getTrend());
    }

    #[Test]
    public function itHasDefaultColsOfThree(): void
    {
        $widget = StatsWidget::make($this->createMock(Environment::class))
            ->name('users')
            ->label('Users')
            ->value(10);

        $this->assertSame(3, $widget->getCols());
    }

    #[Test]
    public function itHasDefaultPriorityOfZero(): void
    {
        $widget = StatsWidget::make($this->createMock(Environment::class))
            ->name('users')
            ->label('Users')
            ->value(10);

        $this->assertSame(0, $widget->getPriority());
    }

    #[Test]
    public function itRendersViaTwig(): void
    {
        $twig = $this->createMock(Environment::class);
        $twig->expects($this->once())
            ->method('render')
            ->with(
                '@BeaconAdmin/widgets/stats.html.twig',
                [
                    'label' => 'Users',
                    'value' => 42,
                    'icon' => null,
                    'trend' => null,
                    'trendLabel' => null,
                ],
            )
            ->willReturn('<div>rendered</div>');

        $widget = StatsWidget::make($twig)
            ->name('users')
            ->label('Users')
            ->value(42);

        $this->assertSame('<div>rendered</div>', $widget->render());
    }
}
