<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Menu;

use Devgeek\BeaconAdmin\Menu\MenuItem;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class MenuItemTest extends TestCase
{
    #[Test]
    public function itCreatesViaMake(): void
    {
        $item = MenuItem::make();

        $this->assertInstanceOf(MenuItem::class, $item);
    }

    #[Test]
    public function itSetsLabelFluently(): void
    {
        $item = MenuItem::make()->label('Dashboard');

        $this->assertSame('Dashboard', $item->getLabel());
    }

    #[Test]
    public function itSetsRouteFluently(): void
    {
        $item = MenuItem::make()->route('beacon_admin.dashboard');

        $this->assertSame('beacon_admin.dashboard', $item->getRoute());
    }

    #[Test]
    public function itSetsIconFluently(): void
    {
        $item = MenuItem::make()->icon('fas fa-home');

        $this->assertSame('fas fa-home', $item->getIcon());
    }

    #[Test]
    public function itSetsRoleFluently(): void
    {
        $item = MenuItem::make()->role('ROLE_ADMIN');

        $this->assertSame('ROLE_ADMIN', $item->getRole());
    }

    #[Test]
    public function itChainsAllSetters(): void
    {
        $item = MenuItem::make()
            ->label('Users')
            ->route('beacon_admin.users')
            ->icon('fas fa-users')
            ->role('ROLE_ADMIN');

        $this->assertSame('Users', $item->getLabel());
        $this->assertSame('beacon_admin.users', $item->getRoute());
        $this->assertSame('fas fa-users', $item->getIcon());
        $this->assertSame('ROLE_ADMIN', $item->getRole());
    }

    #[Test]
    public function itHasNoChildrenByDefault(): void
    {
        $item = MenuItem::make()->label('Settings');

        $this->assertFalse($item->hasChildren());
        $this->assertSame([], $item->getChildren());
    }

    #[Test]
    public function itSetsChildren(): void
    {
        $child = MenuItem::make()->label('Profile')->route('beacon_admin.profile');
        $parent = MenuItem::make()
            ->label('Settings')
            ->children([$child]);

        $this->assertTrue($parent->hasChildren());
        $this->assertCount(1, $parent->getChildren());
        $this->assertSame('Profile', $parent->getChildren()[0]->getLabel());
    }

    #[Test]
    public function itDefaultsRouteToNull(): void
    {
        $item = MenuItem::make()->label('Home');

        $this->assertNull($item->getRoute());
    }

    #[Test]
    public function itDefaultsIconToNull(): void
    {
        $item = MenuItem::make()->label('Home');

        $this->assertNull($item->getIcon());
    }

    #[Test]
    public function itDefaultsRoleToNull(): void
    {
        $item = MenuItem::make()->label('Home');

        $this->assertNull($item->getRole());
    }
}
