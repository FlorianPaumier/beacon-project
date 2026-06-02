<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Table\Filter;

use Devgeek\BeaconAdmin\Table\Filter\Filter;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class FilterTest extends TestCase
{
    #[Test]
    public function itSetsNameFluently(): void
    {
        $filter = $this->createConcreteFilter();
        $filter->name('status');

        $this->assertSame('status', $filter->getName());
    }

    #[Test]
    public function itSetsLabelFluently(): void
    {
        $filter = $this->createConcreteFilter();
        $filter->name('status')->label('Status');

        $this->assertSame('Status', $filter->getLabel());
    }

    #[Test]
    public function itReturnsNullLabelByDefault(): void
    {
        $filter = $this->createConcreteFilter();
        $filter->name('status');

        $this->assertNull($filter->getLabel());
    }

    #[Test]
    public function itEvaluatesClosureLabel(): void
    {
        $filter = $this->createConcreteFilter();
        $filter->name('status')->label(fn () => 'Dynamic');

        $this->assertSame('Dynamic', $filter->getLabel());
    }

    #[Test]
    public function itIsAbstract(): void
    {
        $this->assertTrue((new \ReflectionClass(Filter::class))->isAbstract());
    }

    private function createConcreteFilter(): Filter
    {
        return new class extends Filter {
            public function apply(QueryBuilder $queryBuilder, mixed $value): void
            {
            }
        };
    }
}
