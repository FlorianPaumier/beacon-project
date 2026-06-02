<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Table\Filter;

use Doctrine\ORM\QueryBuilder;

class SelectFilter extends Filter
{
    public function apply(QueryBuilder $queryBuilder, mixed $value): void
    {
        if ($value === null || $value === '' || (is_array($value) && count($value) === 0)) {
            return;
        }

        $parameterName = 'filter_'.$this->name;

        $values = is_array($value) ? $value : [$value];

        $queryBuilder
            ->andWhere($queryBuilder->expr()->in('o.'.$this->name, ':'.$parameterName))
            ->setParameter($parameterName, $values);
    }
}
