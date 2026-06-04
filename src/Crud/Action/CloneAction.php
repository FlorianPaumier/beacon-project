<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Crud\Action;

use Devgeek\BeaconAdmin\Crud\CrudConfig;
use Devgeek\BeaconAdmin\Crud\Event\AfterCreateEvent;
use Devgeek\BeaconAdmin\Crud\Event\BeforeCreateEvent;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Clones a Doctrine entity: copies scalar fields and toOne associations,
 * resets the primary key and timestamp fields (createdAt/updatedAt), and
 * starts toMany collections empty.
 *
 * What is copied:
 *  - Scalar fields (string, int, bool, decimal, etc.)
 *  - toOne associations (ManyToOne, OneToOne) -- the same target reference is reused
 *
 * What is reset:
 *  - Primary key (Doctrine treats the result as a new row)
 *  - `createdAt` and `updatedAt` to the current time, when those properties exist
 *  - toMany collections (OneToMany, ManyToMany) start empty on the clone
 *
 * The action fires BeforeCreateEvent and AfterCreateEvent so existing
 * create listeners are reused transparently.
 */
final readonly class CloneAction
{
    public static function make(): static
    {
        return new static();
    }

    public function clone(object $entity, EntityManagerInterface $em, EventDispatcherInterface $dispatcher): object
    {
        $class = $entity::class;
        $metadata = $em->getClassMetadata($class);

        $clone = clone $entity;

        $this->resetIdentifier($clone, $metadata);
        $this->resetTimestamps($clone);
        $this->clearCollections($clone, $metadata);

        $config = CrudConfig::make()->entityClass($class);

        $dispatcher->dispatch(new BeforeCreateEvent($clone, $config));

        $em->persist($clone);
        $em->flush();

        $dispatcher->dispatch(new AfterCreateEvent($clone, $config));

        return $clone;
    }

    /**
     * @param ClassMetadata<object> $metadata
     */
    private function resetIdentifier(object $entity, ClassMetadata $metadata): void
    {
        foreach ($metadata->getIdentifierFieldNames() as $field) {
            $this->setPropertyValue($entity, $field, null);
        }
    }

    private function resetTimestamps(object $entity): void
    {
        $timestampFields = ['createdAt', 'updatedAt'];
        $reflection = new \ReflectionClass($entity);

        foreach ($reflection->getProperties() as $property) {
            if (!\in_array($property->getName(), $timestampFields, true)) {
                continue;
            }

            $type = $property->getType();

            if (!$type instanceof \ReflectionNamedType) {
                continue;
            }

            $typeName = $type->getName();

            if (!is_a($typeName, \DateTimeInterface::class, true)) {
                continue;
            }

            if (!$property->isInitialized($entity)) {
                continue;
            }

            $now = \DateTimeImmutable::class === $typeName || \DateTimeInterface::class === $typeName
                ? new \DateTimeImmutable()
                : new \DateTime();

            $this->setPropertyValue($entity, $property->getName(), $now);
        }
    }

    private function getProperty(object $entity, string $name): \ReflectionProperty
    {
        return (new \ReflectionClass($entity))->getProperty($name);
    }

    private function setPropertyValue(object $entity, string $name, mixed $value): void
    {
        $property = $this->getProperty($entity, $name);
        $property->setValue($entity, $value);
    }

    /** @param ClassMetadata<object> $metadata */
    private function clearCollections(object $entity, ClassMetadata $metadata): void
    {
        $reflection = new \ReflectionClass($entity);

        foreach ($metadata->getAssociationNames() as $name) {
            $mapping = $metadata->getAssociationMapping($name);
            $type = $mapping['type'];

            if ($type !== ClassMetadata::ONE_TO_MANY && $type !== ClassMetadata::MANY_TO_MANY) {
                continue;
            }

            if (!$reflection->hasProperty($name)) {
                continue;
            }

            $property = $reflection->getProperty($name);
            $propertyType = $property->getType();

            if (!$propertyType instanceof \ReflectionNamedType) {
                continue;
            }

            if (!is_a($propertyType->getName(), \Doctrine\Common\Collections\Collection::class, true)) {
                continue;
            }

            $this->setPropertyValue($entity, $name, new ArrayCollection());
        }
    }
}
