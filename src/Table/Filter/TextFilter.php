<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Table\Filter;

use Doctrine\ORM\QueryBuilder;

class TextFilter extends Filter
{
    public function apply(QueryBuilder $queryBuilder, mixed $value): void
    {
        if ($value === null || $value === '') {
            return;
        }

        $parameterName = 'filter_'.$this->name;

        $queryBuilder
            ->andWhere($queryBuilder->expr()->like('o.'.$this->name, ':'.$parameterName))
            ->setParameter($parameterName, '%'.$value.'%');
    }
}
