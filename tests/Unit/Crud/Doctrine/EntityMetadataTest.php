<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Crud\Doctrine;

use Devgeek\BeaconAdmin\Crud\Doctrine\AssociationMetadata;
use Devgeek\BeaconAdmin\Crud\Doctrine\EntityMetadata;
use Devgeek\BeaconAdmin\Crud\Doctrine\FieldMetadata;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class EntityMetadataTest extends TestCase
{
    #[Test]
    public function itStoresClassName(): void
    {
        $metadata = EntityMetadata::make()->className('App\Entity\User');

        $this->assertSame('App\Entity\User', $metadata->getClassName());
    }

    #[Test]
    public function itStoresTableName(): void
    {
        $metadata = EntityMetadata::make()->tableName('users');

        $this->assertSame('users', $metadata->getTableName());
    }

    #[Test]
    public function itStoresFields(): void
    {
        $fields = [
            FieldMetadata::make()->name('id')->type('integer'),
            FieldMetadata::make()->name('email')->type('string'),
        ];

        $metadata = EntityMetadata::make()->fields($fields);

        $this->assertCount(2, $metadata->getFields());
        $this->assertSame(['id', 'email'], $metadata->getFieldNames());
    }

    #[Test]
    public function itStoresIdentifier(): void
    {
        $id = FieldMetadata::make()->name('id')->type('integer');
        $metadata = EntityMetadata::make()->identifier([$id]);

        $this->assertCount(1, $metadata->getIdentifier());
        $this->assertSame('id', $metadata->getIdentifier()[0]->getName());
    }

    #[Test]
    public function itStoresAssociations(): void
    {
        $assoc = AssociationMetadata::make()->name('parent')->type('MANY_TO_ONE');
        $metadata = EntityMetadata::make()->associations([$assoc]);

        $this->assertCount(1, $metadata->getAssociations());
        $this->assertSame('parent', $metadata->getAssociations()[0]->getName());
    }

    #[Test]
    public function itDefaultsToEmptyCollections(): void
    {
        $metadata = EntityMetadata::make();

        $this->assertSame([], $metadata->getFields());
        $this->assertSame([], $metadata->getIdentifier());
        $this->assertSame([], $metadata->getAssociations());
        $this->assertSame([], $metadata->getFieldNames());
    }

    #[Test]
    public function itChainsAllSetters(): void
    {
        $metadata = EntityMetadata::make()
            ->className('App\Entity\Product')
            ->tableName('products')
            ->fields([FieldMetadata::make()->name('id')->type('integer')])
            ->identifier([FieldMetadata::make()->name('id')->type('integer')])
            ->associations([]);

        $this->assertSame('App\Entity\Product', $metadata->getClassName());
        $this->assertSame('products', $metadata->getTableName());
        $this->assertCount(1, $metadata->getFields());
        $this->assertCount(1, $metadata->getIdentifier());
    }
}
