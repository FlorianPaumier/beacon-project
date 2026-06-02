<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Crud\Form\Action;

use Devgeek\BeaconAdmin\Crud\CrudConfig;
use Devgeek\BeaconAdmin\Crud\Event\AfterUpdateEvent;
use Devgeek\BeaconAdmin\Crud\Event\BeforeUpdateEvent;
use Devgeek\BeaconAdmin\Crud\Form\FormBuilder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Twig\Environment;

class UpdateAction
{
    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected FormBuilder $formBuilder,
        protected EventDispatcherInterface $eventDispatcher,
        protected Environment $twig,
    ) {
    }

    public function handle(Request $request, string $id, CrudConfig $config): Response
    {
        $entityClass = $config->getEntityClass();
        $entity = $this->entityManager->getRepository($entityClass)->find($id);

        if ($entity === null) {
            throw new NotFoundHttpException('Entity not found');
        }

        $this->eventDispatcher->dispatch(new BeforeUpdateEvent($entity, $config));

        $form = $this->formBuilder->build($entity, $config);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->eventDispatcher->dispatch(new AfterUpdateEvent($entity, $config));

            return new RedirectResponse($request->getUri());
        }

        return new Response($this->twig->render('@BeaconAdmin/crud/form.html.twig', [
            'config' => $config,
            'form' => $form->createView(),
            'entity' => $entity,
            'action' => 'update',
        ]));
    }
}
