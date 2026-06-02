<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Form\Component;

use Devgeek\BeaconAdmin\Form\Component\Search;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class SearchTest extends TestCase
{
    #[Test]
    public function itCreatesViaMake(): void
    {
        $input = Search::make();

        $this->assertInstanceOf(Search::class, $input);
    }

    #[Test]
    public function itSetsNameFluently(): void
    {
        $input = Search::make()->name('query');

        $this->assertSame('query', $input->getName());
    }

    #[Test]
    public function itSetsLabelFluently(): void
    {
        $input = Search::make()->name('query')->label('Search');

        $this->assertSame('Search', $input->getLabel());
    }

    #[Test]
    public function itEvaluatesClosureLabel(): void
    {
        $input = Search::make()->name('query')->label(fn () => 'Find');

        $this->assertSame('Find', $input->getLabel());
    }

    #[Test]
    public function itReturnsNullPlaceholderByDefault(): void
    {
        $input = Search::make()->name('query');

        $this->assertNull($input->getPlaceholder());
    }

    #[Test]
    public function itSetsPlaceholderFluently(): void
    {
        $input = Search::make()->name('query')->placeholder('Search...');

        $this->assertSame('Search...', $input->getPlaceholder());
    }

    #[Test]
    public function itEvaluatesClosurePlaceholder(): void
    {
        $input = Search::make()->name('query')->placeholder(fn () => 'Type to search');

        $this->assertSame('Type to search', $input->getPlaceholder());
    }

    #[Test]
    public function itChainsAllSetters(): void
    {
        $input = Search::make()
            ->name('search_query')
            ->label('Search Query')
            ->placeholder('Find something...');

        $this->assertSame('search_query', $input->getName());
        $this->assertSame('Search Query', $input->getLabel());
        $this->assertSame('Find something...', $input->getPlaceholder());
    }
}
