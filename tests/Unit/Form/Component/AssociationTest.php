<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Form\Component;

use Devgeek\BeaconAdmin\Form\Component\Association;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class AssociationTest extends TestCase
{
    #[Test]
    public function itCreatesViaMake(): void
    {
        $input = Association::make();

        $this->assertInstanceOf(Association::class, $input);
    }

    #[Test]
    public function itSetsNameFluently(): void
    {
        $input = Association::make()->name('category');

        $this->assertSame('category', $input->getName());
    }

    #[Test]
    public function itSetsLabelFluently(): void
    {
        $input = Association::make()->name('category')->label('Category');

        $this->assertSame('Category', $input->getLabel());
    }

    #[Test]
    public function itEvaluatesClosureLabel(): void
    {
        $input = Association::make()->name('category')->label(fn () => 'Related Category');

        $this->assertSame('Related Category', $input->getLabel());
    }

    #[Test]
    public function itDefaultsRequiredToFalse(): void
    {
        $input = Association::make()->name('category');

        $this->assertFalse($input->isRequired());
    }

    #[Test]
    public function itSetsRequiredFluently(): void
    {
        $input = Association::make()->name('category')->required();

        $this->assertTrue($input->isRequired());
    }

    #[Test]
    public function itEvaluatesClosureRequired(): void
    {
        $input = Association::make()->name('category')->required(fn () => true);

        $this->assertTrue($input->isRequired());
    }

    #[Test]
    public function itReturnsNullTargetEntityByDefault(): void
    {
        $input = Association::make()->name('category');

        $this->assertNull($input->getTargetEntity());
    }

    #[Test]
    public function itSetsTargetEntityFluently(): void
    {
        $input = Association::make()->name('category')->targetEntity('App\Entity\Category');

        $this->assertSame('App\Entity\Category', $input->getTargetEntity());
    }

    #[Test]
    public function itEvaluatesClosureTargetEntity(): void
    {
        $input = Association::make()->name('category')->targetEntity(fn () => 'App\Entity\Product');

        $this->assertSame('App\Entity\Product', $input->getTargetEntity());
    }

    #[Test]
    public function itDefaultsMultipleToFalse(): void
    {
        $input = Association::make()->name('category');

        $this->assertFalse($input->isMultiple());
    }

    #[Test]
    public function itSetsMultipleFluently(): void
    {
        $input = Association::make()->name('category')->multiple();

        $this->assertTrue($input->isMultiple());
    }

    #[Test]
    public function itEvaluatesClosureMultiple(): void
    {
        $input = Association::make()->name('category')->multiple(fn () => true);

        $this->assertTrue($input->isMultiple());
    }

    #[Test]
    public function itDefaultsSearchableToFalse(): void
    {
        $input = Association::make()->name('category');

        $this->assertFalse($input->isSearchable());
    }

    #[Test]
    public function itSetsSearchableFluently(): void
    {
        $input = Association::make()->name('category')->searchable();

        $this->assertTrue($input->isSearchable());
    }

    #[Test]
    public function itEvaluatesClosureSearchable(): void
    {
        $input = Association::make()->name('category')->searchable(fn () => true);

        $this->assertTrue($input->isSearchable());
    }

    #[Test]
    public function itChainsAllSetters(): void
    {
        $input = Association::make()
            ->name('tags')
            ->label('Tags')
            ->required()
            ->targetEntity('App\Entity\Tag')
            ->multiple()
            ->searchable();

        $this->assertSame('tags', $input->getName());
        $this->assertSame('Tags', $input->getLabel());
        $this->assertTrue($input->isRequired());
        $this->assertSame('App\Entity\Tag', $input->getTargetEntity());
        $this->assertTrue($input->isMultiple());
        $this->assertTrue($input->isSearchable());
    }
}
