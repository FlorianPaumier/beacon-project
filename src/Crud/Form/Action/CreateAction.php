<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Crud\Form\Action;

use Devgeek\BeaconAdmin\Crud\CrudConfig;
use Devgeek\BeaconAdmin\Crud\Doctrine\EntityIntrospector;
use Devgeek\BeaconAdmin\Crud\Event\AfterCreateEvent;
use Devgeek\BeaconAdmin\Crud\Event\BeforeCreateEvent;
use Devgeek\BeaconAdmin\Crud\Form\FormBuilder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class CreateAction
{
    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected FormBuilder $formBuilder,
        protected EntityIntrospector $introspector,
        protected EventDispatcherInterface $eventDispatcher,
        protected Environment $twig,
    ) {
    }

    public function handle(Request $request, CrudConfig $config): Response
    {
        $entityClass = $config->getEntityClass();
        $entity = new $entityClass();

        $this->eventDispatcher->dispatch(new BeforeCreateEvent($entity, $config));

        $form = $this->formBuilder->build($entity, $config);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($entity);
            $this->entityManager->flush();

            $this->eventDispatcher->dispatch(new AfterCreateEvent($entity, $config));

            return new RedirectResponse($request->getUri());
        }

        return new Response($this->twig->render('@BeaconAdmin/crud/form.html.twig', [
            'config' => $config,
            'form' => $form->createView(),
            'entity' => $entity,
            'action' => 'create',
        ]));
    }
}
