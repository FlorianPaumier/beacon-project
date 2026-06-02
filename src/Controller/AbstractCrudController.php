<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Controller;

use Devgeek\BeaconAdmin\Crud\CrudConfig;
use Devgeek\BeaconAdmin\Crud\Doctrine\EntityIntrospector;
use Devgeek\BeaconAdmin\Crud\Event\AfterCreateEvent;
use Devgeek\BeaconAdmin\Crud\Event\AfterDeleteEvent;
use Devgeek\BeaconAdmin\Crud\Event\AfterUpdateEvent;
use Devgeek\BeaconAdmin\Crud\Event\BeforeCreateEvent;
use Devgeek\BeaconAdmin\Crud\Event\BeforeDeleteEvent;
use Devgeek\BeaconAdmin\Crud\Event\BeforeUpdateEvent;
use Devgeek\BeaconAdmin\Crud\Form\FormBuilder;
use Devgeek\BeaconAdmin\Crud\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Service\Attribute\Required;

abstract class AbstractCrudController extends AbstractController
{
    protected EntityManagerInterface $entityManager;
    protected EntityIntrospector $introspector;
    protected PaginationService $paginationService;
    protected FormBuilder $formBuilder;
    protected EventDispatcherInterface $eventDispatcher;

    #[Required]
    public function autowireBeaconCrud(
        EntityManagerInterface $entityManager,
        EntityIntrospector $introspector,
        PaginationService $paginationService,
        FormBuilder $formBuilder,
        EventDispatcherInterface $eventDispatcher,
    ): void {
        $this->entityManager = $entityManager;
        $this->introspector = $introspector;
        $this->paginationService = $paginationService;
        $this->formBuilder = $formBuilder;
        $this->eventDispatcher = $eventDispatcher;
    }

    abstract protected function configureCrud(CrudConfig $config): void;

    /** @return class-string */
    abstract protected function getEntityClass(): string;

    public function getCrudConfig(): CrudConfig
    {
        $config = CrudConfig::make()->entityClass($this->getEntityClass());

        $this->configureCrud($config);

        return $config;
    }

    #[Route('/', name: 'list', methods: ['GET'])]
    public function list(Request $request): Response
    {
        $config = $this->getCrudConfig();
        $repository = $this->entityManager->getRepository($this->getEntityClass());
        $method = $config->getRepositoryMethod();
        $queryBuilder = $method !== null ? $repository->$method() : $repository->createQueryBuilder('e');
        $config->applyQueryModifiers($queryBuilder);

        $result = $this->paginationService->paginateFromConfig($queryBuilder, $request, $config);

        return $this->render('@BeaconAdmin/crud/list.html.twig', [
            'config' => $config,
            'result' => $result,
        ]);
    }

    #[Route('/new', name: 'create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        $config = $this->getCrudConfig();
        $entityClass = $this->getEntityClass();
        $entity = new $entityClass();

        $this->eventDispatcher->dispatch(new BeforeCreateEvent($entity, $config));

        $form = $this->formBuilder->build($entity, $config);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($entity);
            $this->entityManager->flush();

            $this->eventDispatcher->dispatch(new AfterCreateEvent($entity, $config));

            $this->addFlash('success', $config->getEntityLabel().' created successfully.');

            return $this->redirectToRoute($this->getListRoute($request));
        }

        return $this->render('@BeaconAdmin/crud/form.html.twig', [
            'config' => $config,
            'form' => $form,
            'entity' => $entity,
            'action' => 'create',
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function update(Request $request, string $id): Response
    {
        $config = $this->getCrudConfig();
        $entity = $this->entityManager->getRepository($this->getEntityClass())->find($id);

        if ($entity === null) {
            throw $this->createNotFoundException('Entity not found.');
        }

        $this->eventDispatcher->dispatch(new BeforeUpdateEvent($entity, $config));

        $form = $this->formBuilder->build($entity, $config);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->eventDispatcher->dispatch(new AfterUpdateEvent($entity, $config));

            $this->addFlash('success', $config->getEntityLabel().' updated successfully.');

            return $this->redirectToRoute($this->getListRoute($request));
        }

        return $this->render('@BeaconAdmin/crud/form.html.twig', [
            'config' => $config,
            'form' => $form,
            'entity' => $entity,
            'action' => 'update',
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, string $id): Response
    {
        $config = $this->getCrudConfig();
        $entity = $this->entityManager->getRepository($this->getEntityClass())->find($id);

        if ($entity === null) {
            throw $this->createNotFoundException('Entity not found.');
        }

        $submittedToken = (string) $request->request->get('_token', '');

        if (!$this->isCsrfTokenValid('delete'.$id, $submittedToken)) {
            throw $this->createAccessDeniedException('Invalid CSRF token.');
        }

        $this->eventDispatcher->dispatch(new BeforeDeleteEvent($entity, $config));

        $this->entityManager->remove($entity);
        $this->entityManager->flush();

        $this->eventDispatcher->dispatch(new AfterDeleteEvent($entity, $config));

        $this->addFlash('success', $config->getEntityLabel().' deleted successfully.');

        return $this->redirectToRoute($this->getListRoute($request));
    }

    protected function getListRoute(Request $request): string
    {
        $currentRoute = (string) $request->attributes->get('_route', '');

        $result = preg_replace('/_(create|edit|delete|bulk|toggle)$/', '_list', $currentRoute);

        return $result !== null ? $result : $currentRoute;
    }

    #[Route('/{id}/toggle/{field}', name: 'toggle', methods: ['POST'], requirements: ['field' => '\w+'])]
    public function toggleBoolean(Request $request, string $id, string $field): JsonResponse
    {
        $config = $this->getCrudConfig();
        $entity = $this->entityManager->getRepository($this->getEntityClass())->find($id);

        if ($entity === null) {
            return $this->json(['success' => false, 'error' => 'Entity not found.'], Response::HTTP_NOT_FOUND);
        }

        $submittedToken = (string) $request->request->get('_token', '');
        if (!$this->isCsrfTokenValid('toggle_'.$id.'_'.$field, $submittedToken)) {
            return $this->json(['success' => false, 'error' => 'Invalid CSRF token.'], Response::HTTP_FORBIDDEN);
        }

        $refl = new \ReflectionClass($entity);
        if (!$refl->hasProperty($field)) {
            return $this->json(['success' => false, 'error' => 'Field not found.'], Response::HTTP_BAD_REQUEST);
        }

        $currentValue = (bool) $refl->getProperty($field)->getValue($entity);
        $refl->getProperty($field)->setValue($entity, !$currentValue);

        $this->eventDispatcher->dispatch(new BeforeUpdateEvent($entity, $config));

        $this->entityManager->flush();

        $this->eventDispatcher->dispatch(new AfterUpdateEvent($entity, $config));

        return $this->json(['success' => true, 'value' => !$currentValue]);
    }

    #[Route('/bulk', name: 'bulk', methods: ['POST'])]
    public function bulkDelete(Request $request): Response
    {
        $config = $this->getCrudConfig();

        $submittedToken = (string) $request->request->get('_token', '');
        if (!$this->isCsrfTokenValid('bulk_delete', $submittedToken)) {
            throw $this->createAccessDeniedException('Invalid CSRF token.');
        }

        /** @var string[] $ids */
        $ids = $request->request->all('ids');

        if ($ids === []) {
            $this->addFlash('warning', 'No items selected.');

            return $this->redirectToRoute($this->getListRoute($request));
        }

        $entityClass = $this->getEntityClass();
        $repository = $this->entityManager->getRepository($entityClass);
        $identifier = $this->entityManager->getClassMetadata($entityClass)->getIdentifier();
        $idField = $identifier[0];
        $entities = $repository->findBy([$idField => $ids]);

        foreach ($entities as $entity) {
            $this->eventDispatcher->dispatch(new BeforeDeleteEvent($entity, $config));
        }

        foreach ($entities as $entity) {
            $this->entityManager->remove($entity);
        }
        $this->entityManager->flush();

        foreach ($entities as $entity) {
            $this->eventDispatcher->dispatch(new AfterDeleteEvent($entity, $config));
        }

        $count = count($entities);
        $this->addFlash('success', sprintf(
            '%d %s deleted successfully.',
            $count,
            $count === 1 ? $config->getEntityLabel() : $config->getEntityLabelPlural(),
        ));

        return $this->redirectToRoute($this->getListRoute($request));
    }
}
