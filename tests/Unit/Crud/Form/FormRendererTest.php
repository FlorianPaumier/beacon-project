<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Crud\Form;

use Devgeek\BeaconAdmin\Crud\Form\FormRenderer;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Twig\Environment;

final class FormRendererTest extends TestCase
{
    #[Test]
    public function itRendersUsingTwigTemplate(): void
    {
        $twig = $this->createMock(Environment::class);
        $form = $this->createMock(FormInterface::class);
        $formView = new FormView();

        $form->expects($this->once())
            ->method('createView')
            ->willReturn($formView);

        $twig->expects($this->once())
            ->method('render')
            ->with('@BeaconAdmin/crud/form.html.twig', [
                'form' => $formView,
                'action' => 'create',
                'entityLabel' => 'User',
            ])
            ->willReturn('<form>rendered</form>');

        $renderer = new FormRenderer($twig);
        $output = $renderer->render($form, 'create', 'User');

        $this->assertSame('<form>rendered</form>', $output);
    }

    #[Test]
    public function itCanBeInstantiated(): void
    {
        $twig = $this->createMock(Environment::class);
        $renderer = new FormRenderer($twig);

        $this->assertInstanceOf(FormRenderer::class, $renderer);
    }
}
