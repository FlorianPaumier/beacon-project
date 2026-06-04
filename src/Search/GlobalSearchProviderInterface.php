<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Search;

interface GlobalSearchProviderInterface
{
    public function getLabel(): string;

    /** @return array<int, array{title: string, url: string, description?: string}> */
    public function search(string $query): array;
}
