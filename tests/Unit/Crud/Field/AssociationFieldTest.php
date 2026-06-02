<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Crud\Field;

use Devgeek\BeaconAdmin\Crud\Field\AssociationField;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

final class AssociationFieldTest extends TestCase
{
    #[Test]
    public function itCreatesViaMake(): void
    {
        $field = AssociationField::make('category');

        $this->assertInstanceOf(AssociationField::class, $field);
    }

    #[Test]
    public function itAutoGeneratesLabel(): void
    {
        $field = AssociationField::make('main_category');

        $this->assertSame('Main category', $field->getLabel());
    }

    #[Test]
    public function itSetsTargetEntity(): void
    {
        $field = AssociationField::make('category')->targetEntity('App\Entity\Category');

        $this->assertSame('App\Entity\Category', $field->getTargetEntity());
    }

    #[Test]
    public function itDefaultsMultipleToFalse(): void
    {
        $field = AssociationField::make('category')->targetEntity('App\Entity\Category');

        $this->assertFalse($field->getIsMultiple());
    }

    #[Test]
    public function itSetsMultiple(): void
    {
        $field = AssociationField::make('tags')
            ->targetEntity('App\Entity\Tag')
            ->isMultiple();

        $this->assertTrue($field->getIsMultiple());
    }

    #[Test]
    public function itSetsMultipleToFalse(): void
    {
        $field = AssociationField::make('category')
            ->targetEntity('App\Entity\Category')
            ->isMultiple(false);

        $this->assertFalse($field->getIsMultiple());
    }

    #[Test]
    public function itReturnsEntityType(): void
    {
        $field = AssociationField::make('category')->targetEntity('App\Entity\Category');

        $this->assertSame(EntityType::class, $field->getFormType());
    }

    #[Test]
    public function itChainsAllSetters(): void
    {
        $field = AssociationField::make('articles')
            ->label('Articles')
            ->required(true)
            ->targetEntity('App\Entity\Article')
            ->isMultiple(true);

        $this->assertSame('articles', $field->getName());
        $this->assertSame('Articles', $field->getLabel());
        $this->assertTrue($field->isRequired());
        $this->assertSame('App\Entity\Article', $field->getTargetEntity());
        $this->assertTrue($field->getIsMultiple());
    }
}
