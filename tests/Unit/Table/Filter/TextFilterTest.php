<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Table\Filter;

use Devgeek\BeaconAdmin\Table\Filter\TextFilter;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query\Expr\Comparison;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class TextFilterTest extends TestCase
{
    #[Test]
    public function itCreatesViaMake(): void
    {
        $filter = TextFilter::make();

        $this->assertNull($filter->getLabel());
    }

    #[Test]
    public function itAppliesLikeConditionForStringValue(): void
    {
        $filter = TextFilter::make()->name('name');
        $expr = $this->createMock(Expr::class);
        $qb = $this->createMock(QueryBuilder::class);

        $qb->expects($this->once())->method('expr')->willReturn($expr);
        $expr->expects($this->once())->method('like')
            ->with('o.name', ':filter_name')
            ->willReturn(new Comparison('o.name', 'LIKE', ':filter_name'));
        $qb->expects($this->once())->method('andWhere')
            ->with($this->isInstanceOf(Comparison::class))
            ->willReturn($qb);
        $qb->expects($this->once())->method('setParameter')
            ->with('filter_name', '%John%')
            ->willReturn($qb);

        $filter->apply($qb, 'John');
    }

    #[Test]
    public function itDoesNothingWhenValueIsNull(): void
    {
        $filter = TextFilter::make()->name('name');
        $qb = $this->createMock(QueryBuilder::class);
        $qb->expects($this->never())->method('andWhere');
        $qb->expects($this->never())->method('setParameter');

        $filter->apply($qb, null);
    }

    #[Test]
    public function itDoesNothingWhenValueIsEmptyString(): void
    {
        $filter = TextFilter::make()->name('name');
        $qb = $this->createMock(QueryBuilder::class);
        $qb->expects($this->never())->method('andWhere');

        $filter->apply($qb, '');
    }
}
