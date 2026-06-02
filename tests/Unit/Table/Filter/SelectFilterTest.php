<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Table\Filter;

use Devgeek\BeaconAdmin\Table\Filter\SelectFilter;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query\Expr\Func;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class SelectFilterTest extends TestCase
{
    #[Test]
    public function itCreatesViaMake(): void
    {
        $filter = SelectFilter::make();

        $this->assertNull($filter->getLabel());
    }

    #[Test]
    public function itAppliesInConditionForArrayValue(): void
    {
        $filter = SelectFilter::make()->name('status');
        $expr = $this->createMock(Expr::class);
        $qb = $this->createMock(QueryBuilder::class);

        $qb->expects($this->once())->method('expr')->willReturn($expr);
        $expr->expects($this->once())->method('in')
            ->with('o.status', ':filter_status')
            ->willReturn(new Func('IN', ['o.status', ':filter_status']));
        $qb->expects($this->once())->method('andWhere')
            ->with($this->isInstanceOf(Func::class))
            ->willReturn($qb);
        $qb->expects($this->once())->method('setParameter')
            ->with('filter_status', ['active', 'pending'])
            ->willReturn($qb);

        $filter->apply($qb, ['active', 'pending']);
    }

    #[Test]
    public function itDoesNothingWhenValueIsNull(): void
    {
        $filter = SelectFilter::make()->name('status');
        $qb = $this->createMock(QueryBuilder::class);
        $qb->expects($this->never())->method('andWhere');

        $filter->apply($qb, null);
    }

    #[Test]
    public function itDoesNothingWhenValueIsEmptyString(): void
    {
        $filter = SelectFilter::make()->name('status');
        $qb = $this->createMock(QueryBuilder::class);
        $qb->expects($this->never())->method('andWhere');

        $filter->apply($qb, '');
    }

    #[Test]
    public function itDoesNothingWhenValueIsEmptyArray(): void
    {
        $filter = SelectFilter::make()->name('status');
        $qb = $this->createMock(QueryBuilder::class);
        $qb->expects($this->never())->method('andWhere');

        $filter->apply($qb, []);
    }

    #[Test]
    public function itWrapsScalarValueInArray(): void
    {
        $filter = SelectFilter::make()->name('status');
        $expr = $this->createMock(Expr::class);
        $qb = $this->createMock(QueryBuilder::class);

        $qb->method('expr')->willReturn($expr);
        $expr->method('in')->willReturn(new Func('IN', ['o.status', ':filter_status']));
        $qb->method('andWhere')->willReturn($qb);
        $qb->expects($this->once())->method('setParameter')
            ->with('filter_status', ['active'])
            ->willReturn($qb);

        $filter->apply($qb, 'active');
    }
}
