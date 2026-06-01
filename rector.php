<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\PHPUnit\Set\PHPUnitSetList;

return RectorConfig::configure()
    ->withPaths([__DIR__.'/src', __DIR__.'/tests'])
    ->withSkip([__DIR__.'/vendor', __DIR__.'/specs'])
    ->withSets([
        PHPUnitSetList::PHPUNIT_110,
    ])
    ->withPhpSets(php84: true)
;
