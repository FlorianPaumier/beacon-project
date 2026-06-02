<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Crud\Form\Action;

use Devgeek\BeaconAdmin\Crud\CrudConfig;
use Devgeek\BeaconAdmin\Crud\Doctrine\EntityIntrospector;
use Devgeek\BeaconAdmin\Crud\Event\AfterCreateEvent;
use Devgeek\BeaconAdmin\Crud\Event\BeforeCreateEvent;
use Devgeek\BeaconAdmin\Crud\Form\Action\CreateAction;
use Devgeek\BeaconAdmin\Crud\Form\FormBuilder;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

final class CreateActionTest extends TestCase
{
    private function createAction(
        ?EntityManagerInterface $entityManager = null,
        ?FormBuilder $formBuilder = null,
        ?EntityIntrospector $introspector = null,
        ?EventDispatcherInterface $eventDispatcher = null,
        ?Environment $twig = null,
    ): CreateAction {
        return new CreateAction(
            $entityManager ?? $this->createMock(EntityManagerInterface::class),
            $formBuilder ?? $this->createMock(FormBuilder::class),
            $introspector ?? $this->createMock(EntityIntrospector::class),
            $eventDispatcher ?? $this->createMock(EventDispatcherInterface::class),
            $twig ?? $this->createMock(Environment::class),
        );
    }

    #[Test]
    public function itCanBeInstantiated(): void
    {
        $action = $this->createAction();

        $this->assertStringContainsString('CreateAction', get_class($action));
    }

    #[Test]
    public function itCreatesAndPersistsEntityOnValidSubmit(): void
    {
        $config = CrudConfig::make()->entityClass(\stdClass::class);

        $form = $this->createMock(FormInterface::class);
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isSubmitted')->willReturn(true);
        $form->expects($this->once())->method('isValid')->willReturn(true);

        $formBuilder = $this->createMock(FormBuilder::class);
        $formBuilder->expects($this->once())
            ->method('build')
            ->with($this->isInstanceOf(\stdClass::class), $config)
            ->willReturn($form);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->once())->method('persist');
        $entityManager->expects($this->once())->method('flush');

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $eventDispatcher->expects($this->exactly(2))
            ->method('dispatch')
            ->with(
                $this->logicalOr(
                    $this->isInstanceOf(BeforeCreateEvent::class),
                    $this->isInstanceOf(AfterCreateEvent::class),
                ),
            );

        $action = $this->createAction($entityManager, $formBuilder, null, $eventDispatcher);
        $response = $action->handle(new Request(), $config);

        $this->assertSame(Response::HTTP_FOUND, $response->getStatusCode());
    }

    #[Test]
    public function itRendersFormOnGetRequest(): void
    {
        $config = CrudConfig::make()->entityClass(\stdClass::class);

        $formView = new \Symfony\Component\Form\FormView();

        $form = $this->createMock(FormInterface::class);
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isSubmitted')->willReturn(false);
        $form->expects($this->once())->method('createView')->willReturn($formView);

        $formBuilder = $this->createMock(FormBuilder::class);
        $formBuilder->expects($this->once())
            ->method('build')
            ->willReturn($form);

        $twig = $this->createMock(Environment::class);
        $twig->expects($this->once())
            ->method('render')
            ->with('@BeaconAdmin/crud/form.html.twig', $this->arrayHasKey('form'))
            ->willReturn('<form>rendered</form>');

        $action = $this->createAction(null, $formBuilder, null, null, $twig);
        $response = $action->handle(new Request(), $config);

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $this->assertSame('<form>rendered</form>', $response->getContent());
    }

    #[Test]
    public function itRendersFormOnInvalidSubmit(): void
    {
        $config = CrudConfig::make()->entityClass(\stdClass::class);

        $form = $this->createMock(FormInterface::class);
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isSubmitted')->willReturn(true);
        $form->expects($this->once())->method('isValid')->willReturn(false);
        $form->expects($this->once())->method('createView')->willReturn(new \Symfony\Component\Form\FormView());

        $formBuilder = $this->createMock(FormBuilder::class);
        $formBuilder->expects($this->once())
            ->method('build')
            ->willReturn($form);

        $twig = $this->createMock(Environment::class);
        $twig->expects($this->once())
            ->method('render')
            ->willReturn('<form>with errors</form>');

        $action = $this->createAction(null, $formBuilder, null, null, $twig);
        $response = $action->handle(new Request(), $config);

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
    }
}
