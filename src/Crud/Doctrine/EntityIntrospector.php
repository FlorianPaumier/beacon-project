<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Crud\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;

class EntityIntrospector
{
    protected ?EntityManagerInterface $entityManager = null;

    public static function make(): self
    {
        return new self();
    }

    public function __construct(?EntityManagerInterface $entityManager = null)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param class-string $entityClass
     */
    public function introspect(EntityManagerInterface $entityManager, string $entityClass): EntityMetadata
    {
        $metadata = $entityManager->getClassMetadata($entityClass);

        return $this->buildMetadata($metadata);
    }

    /**
     * Convenience: uses the injected EntityManager if available.
     * Prefer `introspect($em, $class)` for explicit dependency.
     */
    public function introspectFromDefault(string $entityClass): EntityMetadata
    {
        if (null === $this->entityManager) {
            throw new \LogicException('No EntityManager available. Use introspect($em, $class) or inject one via constructor.');
        }

        return $this->introspect($this->entityManager, $entityClass);
    }

    /**
     * @param ClassMetadata<object> $metadata
     */
    private function buildMetadata(ClassMetadata $metadata): EntityMetadata
    {
        $fields = $this->extractFields($metadata);
        $identifier = $this->extractIdentifier($metadata, $fields);
        $associations = $this->extractAssociations($metadata);

        return EntityMetadata::make()
            ->className($metadata->getName())
            ->tableName($metadata->getTableName())
            ->fields($fields)
            ->identifier($identifier)
            ->associations($associations);
    }

    /**
     * @param ClassMetadata<object> $metadata
     * @return array<FieldMetadata>
     */
    private function extractFields(ClassMetadata $metadata): array
    {
        $fields = [];

        foreach ($metadata->getFieldNames() as $fieldName) {
            $mapping = $metadata->getFieldMapping($fieldName);

            $fields[] = FieldMetadata::make()
                ->name($fieldName)
                ->type($mapping['type'] ?? 'string')
                ->nullable((bool) ($mapping['nullable'] ?? false))
                ->length(isset($mapping['length']) ? (int) $mapping['length'] : null)
                ->unique((bool) ($mapping['unique'] ?? false));
        }

        return $fields;
    }

    /**
     * @param ClassMetadata<object> $metadata
     * @param array<FieldMetadata> $fields
     * @return array<FieldMetadata>
     */
    private function extractIdentifier(ClassMetadata $metadata, array $fields): array
    {
        $idFields = $metadata->getIdentifierFieldNames();
        $identifier = [];

        foreach ($fields as $field) {
            if (\in_array($field->getName(), $idFields, true)) {
                $identifier[] = $field;
            }
        }

        return $identifier;
    }

    /**
     * @param ClassMetadata<object> $metadata
     * @return array<AssociationMetadata>
     */
    private function extractAssociations(ClassMetadata $metadata): array
    {
        $associations = [];

        foreach ($metadata->getAssociationNames() as $assocName) {
            $mapping = $metadata->getAssociationMapping($assocName);

            $associations[] = AssociationMetadata::make()
                ->name($assocName)
                ->targetEntity($mapping['targetEntity'])
                ->type($this->resolveAssociationType($mapping['type']))
                ->isOwningSide((bool) ($mapping['isOwningSide'] ?? true));
        }

        return $associations;
    }

    private function resolveAssociationType(int $type): string
    {
        return match ($type) {
            ClassMetadata::ONE_TO_ONE => 'ONE_TO_ONE',
            ClassMetadata::ONE_TO_MANY => 'ONE_TO_MANY',
            ClassMetadata::MANY_TO_ONE => 'MANY_TO_ONE',
            ClassMetadata::MANY_TO_MANY => 'MANY_TO_MANY',
            default => 'UNKNOWN',
        };
    }
}
