<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Crud\Form\Action;

use Devgeek\BeaconAdmin\Crud\CrudConfig;
use Devgeek\BeaconAdmin\Crud\Event\AfterDeleteEvent;
use Devgeek\BeaconAdmin\Crud\Event\BeforeDeleteEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Twig\Environment;

class DeleteAction
{
    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected EventDispatcherInterface $eventDispatcher,
        protected Environment $twig,
    ) {
    }

    public function handle(Request $request, string $id, CrudConfig $config, string $redirectUrl): Response
    {
        $entityClass = $config->getEntityClass();
        $entity = $this->entityManager->getRepository($entityClass)->find($id);

        if ($entity === null) {
            throw new NotFoundHttpException('Entity not found');
        }

        $token = (string) $request->request->get('_token', '');

        if (!hash_equals($token, $request->getSession()->getId())) {
            return new Response('Invalid CSRF token', Response::HTTP_FORBIDDEN);
        }

        $this->eventDispatcher->dispatch(new BeforeDeleteEvent($entity, $config));

        $this->entityManager->remove($entity);
        $this->entityManager->flush();

        $this->eventDispatcher->dispatch(new AfterDeleteEvent($entity, $config));

        return new RedirectResponse($redirectUrl);
    }
}
