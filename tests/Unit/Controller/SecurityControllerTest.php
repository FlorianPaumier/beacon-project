<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Controller;

use Devgeek\BeaconAdmin\Controller\SecurityController;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class SecurityControllerTest extends TestCase
{
    #[Test]
    public function itThrowsLogicExceptionOnLogout(): void
    {
        $controller = SecurityController::make();

        $this->expectException(\LogicException::class);

        $controller->logout();
    }
}
