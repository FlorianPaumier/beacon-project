<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Crud\Doctrine;

use Devgeek\BeaconAdmin\Crud\Doctrine\AssociationMetadata;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class AssociationMetadataTest extends TestCase
{
    #[Test]
    public function itCreatesViaMake(): void
    {
        $assoc = AssociationMetadata::make();

        $this->assertTrue($assoc->getIsOwningSide());
    }

    #[Test]
    public function itSetsName(): void
    {
        $assoc = AssociationMetadata::make()->name('comments');

        $this->assertSame('comments', $assoc->getName());
    }

    #[Test]
    public function itSetsTargetEntity(): void
    {
        $assoc = AssociationMetadata::make()->targetEntity('App\Entity\Comment');

        $this->assertSame('App\Entity\Comment', $assoc->getTargetEntity());
    }

    #[Test]
    public function itSetsType(): void
    {
        $assoc = AssociationMetadata::make()->type('ONE_TO_MANY');

        $this->assertSame('ONE_TO_MANY', $assoc->getType());
    }

    #[Test]
    public function itDefaultsIsOwningSideToTrue(): void
    {
        $assoc = AssociationMetadata::make();

        $this->assertTrue($assoc->getIsOwningSide());
    }

    #[Test]
    public function itSetsIsOwningSide(): void
    {
        $assoc = AssociationMetadata::make()->isOwningSide(false);

        $this->assertFalse($assoc->getIsOwningSide());
    }

    #[Test]
    public function itChainsAllSetters(): void
    {
        $assoc = AssociationMetadata::make()
            ->name('articles')
            ->targetEntity('App\Entity\Article')
            ->type('MANY_TO_MANY')
            ->isOwningSide(false);

        $this->assertSame('articles', $assoc->getName());
        $this->assertSame('App\Entity\Article', $assoc->getTargetEntity());
        $this->assertSame('MANY_TO_MANY', $assoc->getType());
        $this->assertFalse($assoc->getIsOwningSide());
    }
}
