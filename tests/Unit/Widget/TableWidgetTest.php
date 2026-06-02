<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Widget;

use Devgeek\BeaconAdmin\Widget\TableWidget;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Twig\Environment;

final class TableWidgetTest extends TestCase
{
    #[Test]
    public function itCreatesViaMake(): void
    {
        $twig = $this->createMock(Environment::class);
        $container = $this->createMock(ContainerInterface::class);
        $widget = TableWidget::make($twig, $container);

        $this->assertInstanceOf(TableWidget::class, $widget);
    }

    #[Test]
    public function itSetsRepositoryAndMethod(): void
    {
        $widget = TableWidget::make(
            $this->createMock(Environment::class),
            $this->createMock(ContainerInterface::class),
        )
            ->name('recent-users')
            ->label('Recent Users')
            ->repository('App\Repository\UserRepository')
            ->method('findRecent');

        $this->assertSame('App\Repository\UserRepository', $widget->getRepository());
        $this->assertSame('findRecent', $widget->getMethod());
    }

    #[Test]
    public function itDefaultsToFiveLimit(): void
    {
        $widget = TableWidget::make(
            $this->createMock(Environment::class),
            $this->createMock(ContainerInterface::class),
        )
            ->name('recent-users')
            ->label('Recent Users')
            ->repository('App\Repository\UserRepository');

        $this->assertSame(5, $widget->getLimit());
    }

    #[Test]
    public function itDefaultsToFindLatestMethod(): void
    {
        $widget = TableWidget::make(
            $this->createMock(Environment::class),
            $this->createMock(ContainerInterface::class),
        )
            ->name('recent-users')
            ->label('Recent Users')
            ->repository('App\Repository\UserRepository');

        $this->assertSame('findLatest', $widget->getMethod());
    }

    #[Test]
    public function itHasDefaultColsOfSix(): void
    {
        $widget = TableWidget::make(
            $this->createMock(Environment::class),
            $this->createMock(ContainerInterface::class),
        )
            ->name('recent-users')
            ->label('Recent Users')
            ->repository('App\Repository\UserRepository');

        $this->assertSame(6, $widget->getCols());
    }

    #[Test]
    public function itRendersWithTwig(): void
    {
        $repository = new class {
            public function findLatest(): array
            {
                return [
                    ['id' => 1, 'name' => 'Alice'],
                    ['id' => 2, 'name' => 'Bob'],
                    ['id' => 3, 'name' => 'Charlie'],
                ];
            }
        };

        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->once())
            ->method('get')
            ->with('App\Repository\UserRepository')
            ->willReturn($repository);

        $twig = $this->createMock(Environment::class);
        $twig->expects($this->once())
            ->method('render')
            ->with(
                '@BeaconAdmin/widgets/table.html.twig',
                [
                    'label' => 'Recent Users',
                    'columns' => [['name' => 'id'], ['name' => 'name']],
                    'data' => [
                        ['id' => 1, 'name' => 'Alice'],
                        ['id' => 2, 'name' => 'Bob'],
                        ['id' => 3, 'name' => 'Charlie'],
                    ],
                    'limit' => 5,
                ],
            )
            ->willReturn('<table>rendered</table>');

        $widget = TableWidget::make($twig, $container)
            ->name('recent-users')
            ->label('Recent Users')
            ->repository('App\Repository\UserRepository')
            ->columns([['name' => 'id'], ['name' => 'name']]);

        $this->assertSame('<table>rendered</table>', $widget->render());
    }
}
