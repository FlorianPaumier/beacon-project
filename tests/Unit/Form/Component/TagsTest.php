<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Form\Component;

use Devgeek\BeaconAdmin\Form\Component\Tags;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class TagsTest extends TestCase
{
    #[Test]
    public function itCreatesViaMake(): void
    {
        $this->assertInstanceOf(Tags::class, Tags::make());
    }

    #[Test]
    public function itSetsNameFluently(): void
    {
        $input = Tags::make()->name('skills');

        $this->assertSame('skills', $input->getName());
    }

    #[Test]
    public function itSetsLabelFluently(): void
    {
        $input = Tags::make()->name('skills')->label('Skills');

        $this->assertSame('Skills', $input->getLabel());
    }

    #[Test]
    public function itEvaluatesClosureLabel(): void
    {
        $input = Tags::make()->name('skills')->label(fn () => 'Tags');

        $this->assertSame('Tags', $input->getLabel());
    }

    #[Test]
    public function itDefaultsRequiredToFalse(): void
    {
        $input = Tags::make()->name('skills');

        $this->assertFalse($input->isRequired());
    }

    #[Test]
    public function itSetsRequiredFluently(): void
    {
        $input = Tags::make()->name('skills')->required();

        $this->assertTrue($input->isRequired());
    }

    #[Test]
    public function itEvaluatesClosureRequired(): void
    {
        $input = Tags::make()->name('skills')->required(fn () => true);

        $this->assertTrue($input->isRequired());
    }

    #[Test]
    public function itReturnsNullPlaceholderByDefault(): void
    {
        $input = Tags::make()->name('skills');

        $this->assertNull($input->getPlaceholder());
    }

    #[Test]
    public function itSetsPlaceholderFluently(): void
    {
        $input = Tags::make()->name('skills')->placeholder('Add skill...');

        $this->assertSame('Add skill...', $input->getPlaceholder());
    }

    #[Test]
    public function itEvaluatesClosurePlaceholder(): void
    {
        $input = Tags::make()->name('skills')->placeholder(fn () => 'Enter tag');

        $this->assertSame('Enter tag', $input->getPlaceholder());
    }

    #[Test]
    public function itReturnsEmptySuggestionsByDefault(): void
    {
        $input = Tags::make()->name('skills');

        $this->assertSame([], $input->getSuggestions());
    }

    #[Test]
    public function itSetsSuggestionsFluently(): void
    {
        $suggestions = ['PHP', 'JS', 'Go'];
        $input = Tags::make()->name('skills')->suggestions($suggestions);

        $this->assertSame($suggestions, $input->getSuggestions());
    }

    #[Test]
    public function itEvaluatesClosureSuggestions(): void
    {
        $input = Tags::make()->name('skills')->suggestions(fn () => ['PHP']);

        $this->assertSame(['PHP'], $input->getSuggestions());
    }

    #[Test]
    public function itReturnsNullMaxTagsByDefault(): void
    {
        $input = Tags::make()->name('skills');

        $this->assertNull($input->getMaxTags());
    }

    #[Test]
    public function itSetsMaxTagsFluently(): void
    {
        $input = Tags::make()->name('skills')->maxTags(5);

        $this->assertSame(5, $input->getMaxTags());
    }

    #[Test]
    public function itEvaluatesClosureMaxTags(): void
    {
        $input = Tags::make()->name('skills')->maxTags(fn () => 10);

        $this->assertSame(10, $input->getMaxTags());
    }

    #[Test]
    public function itDefaultsAllowCustomToTrue(): void
    {
        $input = Tags::make()->name('skills');

        $this->assertTrue($input->isAllowCustom());
    }

    #[Test]
    public function itSetsAllowCustomFluently(): void
    {
        $input = Tags::make()->name('skills')->allowCustom(false);

        $this->assertFalse($input->isAllowCustom());
    }

    #[Test]
    public function itEvaluatesClosureAllowCustom(): void
    {
        $input = Tags::make()->name('skills')->allowCustom(fn () => false);

        $this->assertFalse($input->isAllowCustom());
    }

    #[Test]
    public function itChainsAllSetters(): void
    {
        $input = Tags::make()
            ->name('languages')
            ->label('Languages')
            ->required()
            ->placeholder('Add language...')
            ->suggestions(['PHP', 'Python'])
            ->maxTags(10)
            ->allowCustom(true);

        $this->assertSame('languages', $input->getName());
        $this->assertSame('Languages', $input->getLabel());
        $this->assertTrue($input->isRequired());
        $this->assertSame('Add language...', $input->getPlaceholder());
        $this->assertSame(['PHP', 'Python'], $input->getSuggestions());
        $this->assertSame(10, $input->getMaxTags());
        $this->assertTrue($input->isAllowCustom());
    }
}
