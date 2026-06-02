<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Security;

use Devgeek\BeaconAdmin\Security\BeaconAccess;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class BeaconAccessTest extends TestCase
{
    #[Test]
    public function itCreatesViaMake(): void
    {
        $access = BeaconAccess::make();

        $this->assertNull($access->getRole());
    }

    #[Test]
    public function itSetsRoleFluently(): void
    {
        $access = BeaconAccess::make()->role('ROLE_SUPER_ADMIN');

        $this->assertSame('ROLE_SUPER_ADMIN', $access->getRole());
    }

    #[Test]
    public function itSetsPermissionFluently(): void
    {
        $access = BeaconAccess::make()->permission('entity.edit');

        $this->assertSame('entity.edit', $access->getPermission());
    }

    #[Test]
    public function itChainsMultipleSetters(): void
    {
        $access = BeaconAccess::make()
            ->role('ROLE_ADMIN')
            ->permission('entity.edit');

        $this->assertSame('ROLE_ADMIN', $access->getRole());
        $this->assertSame('entity.edit', $access->getPermission());
    }

    #[Test]
    public function itIsAnPhpAttribute(): void
    {
        $refl = new \ReflectionClass(BeaconAccess::class);
        $attrs = $refl->getAttributes(\Attribute::class);

        $this->assertNotEmpty($attrs);
    }

    #[Test]
    public function itCreatesViaConstructorForAttributes(): void
    {
        $access = new BeaconAccess(role: 'ROLE_ADMIN');

        $this->assertSame('ROLE_ADMIN', $access->getRole());
        $this->assertNull($access->getPermission());
    }

    #[Test]
    public function itDefaultsRoleToNull(): void
    {
        $access = BeaconAccess::make();

        $this->assertNull($access->getRole());
    }

    #[Test]
    public function itDefaultsPermissionToNull(): void
    {
        $access = BeaconAccess::make();

        $this->assertNull($access->getPermission());
    }
}
