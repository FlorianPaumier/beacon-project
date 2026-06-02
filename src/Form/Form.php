<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Form;

use Devgeek\BeaconAdmin\Support\Component as BaseComponent;
use Devgeek\BeaconAdmin\Support\EvaluatesClosures;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

class Form
{
    use EvaluatesClosures;
    /** @var array<BaseComponent> */
    protected array $schema = [];

    protected string|\Closure|null $model = null;

    /** @var array<string, mixed> */
    protected array $state = [];

    protected ?object $entity = null;

    protected readonly EntityManagerInterface $entityManager;

    protected readonly PropertyAccessorInterface $propertyAccessor;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
    }

    /** @param array<BaseComponent> $components */
    public function schema(array $components): static
    {
        $this->schema = $components;

        return $this;
    }

    /** @return array<BaseComponent> */
    public function getSchema(): array
    {
        return $this->schema;
    }

    public function model(string|\Closure $modelClass): static
    {
        $this->model = $modelClass;

        return $this;
    }

    public function getModel(): ?string
    {
        return $this->evaluate($this->model);
    }

    /** @param array<string, mixed> $state */
    public function state(array $state): static
    {
        $this->state = $state;

        return $this;
    }

    /** @return array<string, mixed> */
    public function getState(): array
    {
        return $this->state;
    }

    /** @param array<string, mixed> $data */
    public function fill(array $data): static
    {
        $this->state = $data;

        $modelClass = $this->getModel();

        if ($modelClass !== null && class_exists($modelClass)) {
            $repository = $this->entityManager->getRepository($modelClass);

            $this->entity = $repository->findOneBy($data);

            if ($this->entity === null) {
                $this->entity = new $modelClass();

                $this->applyStateToEntity();
            }
        }

        return $this;
    }

    public function save(): void
    {
        if ($this->entity !== null) {
            $this->applyStateToEntity();

            $this->entityManager->persist($this->entity);
        }

        $this->entityManager->flush();
    }

    protected function applyStateToEntity(): void
    {
        if ($this->entity === null) {
            return;
        }

        foreach ($this->state as $property => $value) {
            $this->propertyAccessor->setValue($this->entity, $property, $value);
        }
    }
}
