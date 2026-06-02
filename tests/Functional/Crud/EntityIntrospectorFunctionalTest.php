<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Functional\Crud;

use Devgeek\BeaconAdmin\Tests\Fixtures\TestApp\Entity\TestEntity;
use Devgeek\BeaconAdmin\Tests\Fixtures\TestApp\TestKernel;
use Devgeek\BeaconAdmin\Tests\Functional\BeaconWebTestCase;

final class EntityIntrospectorFunctionalTest extends BeaconWebTestCase
{
    protected static function getKernelClass(): string
    {
        return TestKernel::class;
    }

    public function testIntrospectsRealEntity(): void
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $introspector = $container->get('Devgeek\BeaconAdmin\Crud\Doctrine\EntityIntrospector');
        $em = $container->get('doctrine.orm.entity_manager');

        $metadata = $introspector->introspect($em, TestEntity::class);

        $this->assertSame(TestEntity::class, $metadata->getClassName());
        $this->assertSame('test_entity', $metadata->getTableName());
        $this->assertCount(1, $metadata->getIdentifier());
        $this->assertSame('id', $metadata->getIdentifier()[0]->getName());
        $this->assertSame('integer', $metadata->getIdentifier()[0]->getType());
    }

    public function testIntrospectsAllFields(): void
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $introspector = $container->get('Devgeek\BeaconAdmin\Crud\Doctrine\EntityIntrospector');
        $em = $container->get('doctrine.orm.entity_manager');

        $metadata = $introspector->introspect($em, TestEntity::class);
        $fieldNames = $metadata->getFieldNames();

        $this->assertContains('id', $fieldNames);
        $this->assertContains('name', $fieldNames);
        $this->assertContains('email', $fieldNames);
        $this->assertContains('active', $fieldNames);
    }

    public function testDetectsUniqueFields(): void
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $introspector = $container->get('Devgeek\BeaconAdmin\Crud\Doctrine\EntityIntrospector');
        $em = $container->get('doctrine.orm.entity_manager');

        $metadata = $introspector->introspect($em, TestEntity::class);

        $emailField = $this->findField($metadata->getFields(), 'email');
        $this->assertNotNull($emailField, 'email field should exist');
        $this->assertTrue($emailField->isUnique(), 'email should be unique');
    }

    public function testDetectsNullableFields(): void
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $introspector = $container->get('Devgeek\BeaconAdmin\Crud\Doctrine\EntityIntrospector');
        $em = $container->get('doctrine.orm.entity_manager');

        $metadata = $introspector->introspect($em, TestEntity::class);

        $activeField = $this->findField($metadata->getFields(), 'active');
        $this->assertNotNull($activeField, 'active field should exist');
        $this->assertTrue($activeField->isNullable(), 'active should be nullable');
    }

    /** @param array<\Devgeek\BeaconAdmin\Crud\Doctrine\FieldMetadata> $fields */
    private function findField(array $fields, string $name): ?\Devgeek\BeaconAdmin\Crud\Doctrine\FieldMetadata
    {
        foreach ($fields as $field) {
            if ($field->getName() === $name) {
                return $field;
            }
        }

        return null;
    }
}
