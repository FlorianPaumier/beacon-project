<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Crud\Form\Action;

use Devgeek\BeaconAdmin\Crud\CrudConfig;
use Devgeek\BeaconAdmin\Crud\Event\AfterUpdateEvent;
use Devgeek\BeaconAdmin\Crud\Event\BeforeUpdateEvent;
use Devgeek\BeaconAdmin\Crud\Form\Action\UpdateAction;
use Devgeek\BeaconAdmin\Crud\Form\FormBuilder;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Twig\Environment;

final class UpdateActionTest extends TestCase
{
    private function createAction(
        ?EntityManagerInterface $entityManager = null,
        ?FormBuilder $formBuilder = null,
        ?EventDispatcherInterface $eventDispatcher = null,
        ?Environment $twig = null,
    ): UpdateAction {
        return new UpdateAction(
            $entityManager ?? $this->createMock(EntityManagerInterface::class),
            $formBuilder ?? $this->createMock(FormBuilder::class),
            $eventDispatcher ?? $this->createMock(EventDispatcherInterface::class),
            $twig ?? $this->createMock(Environment::class),
        );
    }

    #[Test]
    public function itCanBeInstantiated(): void
    {
        $action = $this->createAction();

        $this->assertStringContainsString('UpdateAction', get_class($action));
    }

    #[Test]
    public function itUpdatesAndFlushesOnValidSubmit(): void
    {
        $entity = new \stdClass();
        $config = CrudConfig::make()->entityClass(\stdClass::class);

        $repository = $this->createMock(EntityRepository::class);
        $repository->expects($this->once())
            ->method('find')
            ->with('123')
            ->willReturn($entity);

        $form = $this->createMock(FormInterface::class);
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isSubmitted')->willReturn(true);
        $form->expects($this->once())->method('isValid')->willReturn(true);

        $formBuilder = $this->createMock(FormBuilder::class);
        $formBuilder->expects($this->once())
            ->method('build')
            ->with($entity, $config)
            ->willReturn($form);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->once())
            ->method('getRepository')
            ->with(\stdClass::class)
            ->willReturn($repository);
        $entityManager->expects($this->once())->method('flush');

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $eventDispatcher->expects($this->exactly(2))
            ->method('dispatch')
            ->with(
                $this->logicalOr(
                    $this->isInstanceOf(BeforeUpdateEvent::class),
                    $this->isInstanceOf(AfterUpdateEvent::class),
                ),
            );

        $action = $this->createAction($entityManager, $formBuilder, $eventDispatcher);
        $response = $action->handle(new Request(), '123', $config);

        $this->assertSame(Response::HTTP_FOUND, $response->getStatusCode());
    }

    #[Test]
    public function itThrowsNotFoundWhenEntityMissing(): void
    {
        $config = CrudConfig::make()->entityClass(\stdClass::class);

        $repository = $this->createMock(EntityRepository::class);
        $repository->expects($this->once())
            ->method('find')
            ->with('999')
            ->willReturn(null);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->once())
            ->method('getRepository')
            ->with(\stdClass::class)
            ->willReturn($repository);

        $action = $this->createAction($entityManager);

        $this->expectException(NotFoundHttpException::class);
        $action->handle(new Request(), '999', $config);
    }

    #[Test]
    public function itRendersFormOnGetRequest(): void
    {
        $entity = new \stdClass();
        $config = CrudConfig::make()->entityClass(\stdClass::class);

        $repository = $this->createMock(EntityRepository::class);
        $repository->expects($this->once())
            ->method('find')
            ->with('123')
            ->willReturn($entity);

        $form = $this->createMock(FormInterface::class);
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isSubmitted')->willReturn(false);
        $form->expects($this->once())->method('createView')->willReturn(new \Symfony\Component\Form\FormView());

        $formBuilder = $this->createMock(FormBuilder::class);
        $formBuilder->expects($this->once())
            ->method('build')
            ->willReturn($form);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->once())
            ->method('getRepository')
            ->with(\stdClass::class)
            ->willReturn($repository);

        $twig = $this->createMock(Environment::class);
        $twig->expects($this->once())
            ->method('render')
            ->willReturn('<form>rendered</form>');

        $action = $this->createAction($entityManager, $formBuilder, null, $twig);
        $response = $action->handle(new Request(), '123', $config);

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
    }
}
