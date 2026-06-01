<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Crud\Doctrine;

use Devgeek\BeaconAdmin\Crud\Doctrine\EntityMetadata;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class EntityIntrospectorTest extends TestCase
{
    #[Test]
    public function entityMetadataStoresClassName(): void
    {
        $metadata = EntityMetadata::make()->className('App\Entity\User');

        $this->assertSame('App\Entity\User', $metadata->getClassName());
    }

    #[Test]
    public function entityMetadataStoresTableName(): void
    {
        $metadata = EntityMetadata::make()->tableName('users');

        $this->assertSame('users', $metadata->getTableName());
    }

    #[Test]
    public function entityMetadataCollectsFieldNames(): void
    {
        $metadata = EntityMetadata::make()
            ->className('App\Entity\User')
            ->fields([
                \Devgeek\BeaconAdmin\Crud\Doctrine\FieldMetadata::make()->name('id')->type('integer'),
                \Devgeek\BeaconAdmin\Crud\Doctrine\FieldMetadata::make()->name('email')->type('string'),
            ]);

        $this->assertSame(['id', 'email'], $metadata->getFieldNames());
    }

    #[Test]
    public function entityMetadataDefaultsToEmptyCollections(): void
    {
        $metadata = EntityMetadata::make();

        $this->assertSame([], $metadata->getFields());
        $this->assertSame([], $metadata->getIdentifier());
        $this->assertSame([], $metadata->getAssociations());
        $this->assertSame([], $metadata->getFieldNames());
    }
}
