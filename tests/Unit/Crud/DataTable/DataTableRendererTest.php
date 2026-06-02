<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Crud\DataTable;

use Devgeek\BeaconAdmin\Crud\CrudConfig;
use Devgeek\BeaconAdmin\Crud\DataTable\DataTableRenderer;
use Devgeek\BeaconAdmin\Crud\DataTable\DataTableResult;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Twig\Environment;

final class DataTableRendererTest extends TestCase
{
    #[Test]
    public function itRendersUsingTwigTemplate(): void
    {
        $twig = $this->createMock(Environment::class);
        $result = new DataTableResult([], 0, 1, 10, 1, '', '', '');
        $config = CrudConfig::make();

        $twig->expects($this->once())
            ->method('render')
            ->with('@BeaconAdmin/crud/list.html.twig', [
                'result' => $result,
                'config' => $config,
            ])
            ->willReturn('<html>rendered</html>');

        $renderer = new DataTableRenderer($twig);
        $output = $renderer->render($result, $config);

        $this->assertSame('<html>rendered</html>', $output);
    }

    #[Test]
    public function itCanBeInstantiated(): void
    {
        $twig = $this->createMock(Environment::class);
        $renderer = new DataTableRenderer($twig);

        $this->assertStringContainsString('DataTableRenderer', get_class($renderer));
    }
}
