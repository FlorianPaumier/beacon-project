<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class BeaconWebTestCase extends WebTestCase
{
    protected function setUp(): void
    {
        $this->setInIsolation(true);

        parent::setUp();
    }
}
