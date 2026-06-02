<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Crud\Form\Action;

use Devgeek\BeaconAdmin\Crud\CrudConfig;
use Devgeek\BeaconAdmin\Crud\Event\AfterDeleteEvent;
use Devgeek\BeaconAdmin\Crud\Event\BeforeDeleteEvent;
use Devgeek\BeaconAdmin\Crud\Form\Action\DeleteAction;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Twig\Environment;

final class DeleteActionTest extends TestCase
{
    private function createAction(
        ?EntityManagerInterface $entityManager = null,
        ?EventDispatcherInterface $eventDispatcher = null,
        ?Environment $twig = null,
    ): DeleteAction {
        return new DeleteAction(
            $entityManager ?? $this->createMock(EntityManagerInterface::class),
            $eventDispatcher ?? $this->createMock(EventDispatcherInterface::class),
            $twig ?? $this->createMock(Environment::class),
        );
    }

    private function createRequestWithSession(string $token): Request
    {
        $session = new Session(new MockArraySessionStorage());
        $session->setId('test-session-id');

        $request = new Request([], ['_token' => $token]);
        $request->setSession($session);

        return $request;
    }

    #[Test]
    public function itCanBeInstantiated(): void
    {
        $action = $this->createAction();

        $this->assertInstanceOf(DeleteAction::class, $action);
    }

    #[Test]
    public function itDeletesEntityAndRedirects(): void
    {
        $entity = new \stdClass();
        $config = CrudConfig::make()->entityClass(\stdClass::class);

        $repository = $this->createMock(EntityRepository::class);
        $repository->expects($this->once())
            ->method('find')
            ->with('123')
            ->willReturn($entity);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->once())
            ->method('getRepository')
            ->with(\stdClass::class)
            ->willReturn($repository);
        $entityManager->expects($this->once())
            ->method('remove')
            ->with($entity);
        $entityManager->expects($this->once())
            ->method('flush');

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $eventDispatcher->expects($this->exactly(2))
            ->method('dispatch')
            ->with(
                $this->logicalOr(
                    $this->isInstanceOf(BeforeDeleteEvent::class),
                    $this->isInstanceOf(AfterDeleteEvent::class),
                ),
            );

        $action = $this->createAction($entityManager, $eventDispatcher);
        $response = $action->handle(
            $this->createRequestWithSession('test-session-id'),
            '123',
            $config,
            '/admin/users',
        );

        $this->assertSame(Response::HTTP_FOUND, $response->getStatusCode());
        $this->assertSame('/admin/users', $response->headers->get('Location'));
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
        $action->handle(new Request(), '999', $config, '/admin/users');
    }

    #[Test]
    public function itReturnsForbiddenForInvalidCsrf(): void
    {
        $entity = new \stdClass();
        $config = CrudConfig::make()->entityClass(\stdClass::class);

        $repository = $this->createMock(EntityRepository::class);
        $repository->expects($this->once())
            ->method('find')
            ->with('123')
            ->willReturn($entity);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->once())
            ->method('getRepository')
            ->with(\stdClass::class)
            ->willReturn($repository);
        $entityManager->expects($this->never())
            ->method('remove');

        $action = $this->createAction($entityManager);
        $response = $action->handle(
            $this->createRequestWithSession('wrong-token'),
            '123',
            $config,
            '/admin/users',
        );

        $this->assertSame(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }
}
