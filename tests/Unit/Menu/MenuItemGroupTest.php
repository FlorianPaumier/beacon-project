<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Menu;

use Devgeek\BeaconAdmin\Menu\MenuItem;
use Devgeek\BeaconAdmin\Menu\MenuItemGroup;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class MenuItemGroupTest extends TestCase
{
    #[Test]
    public function itCreatesViaMake(): void
    {
        $group = MenuItemGroup::make();

        $this->assertNull($group->getIcon());
    }

    #[Test]
    public function itSetsLabel(): void
    {
        $group = MenuItemGroup::make()->label('Settings');

        $this->assertSame('Settings', $group->getLabel());
    }

    #[Test]
    public function itSetsIcon(): void
    {
        $group = MenuItemGroup::make()->icon('cog');

        $this->assertSame('cog', $group->getIcon());
    }

    #[Test]
    public function itSetsChildren(): void
    {
        $child = MenuItem::make()->label('Profile')->route('profile');
        $group = MenuItemGroup::make()
            ->label('Settings')
            ->children([$child]);

        $this->assertTrue($group->hasChildren());
        $this->assertCount(1, $group->getChildren());
        $this->assertSame('Profile', $group->getChildren()[0]->getLabel());
    }

    #[Test]
    public function itHasNoChildrenByDefault(): void
    {
        $group = MenuItemGroup::make()->label('Settings');

        $this->assertFalse($group->hasChildren());
        $this->assertSame([], $group->getChildren());
    }

    #[Test]
    public function itAlwaysReturnsNullRoute(): void
    {
        $group = MenuItemGroup::make()->label('Settings');

        $this->assertNull($group->getRoute());
    }

    #[Test]
    public function itAlwaysReturnsNullRole(): void
    {
        $group = MenuItemGroup::make()->label('Settings');

        $this->assertNull($group->getRole());
    }

    #[Test]
    public function itChainsAllSetters(): void
    {
        $child = MenuItem::make()->label('Profile')->route('profile');
        $group = MenuItemGroup::make()
            ->label('Content')
            ->icon('document')
            ->children([$child]);

        $this->assertSame('Content', $group->getLabel());
        $this->assertSame('document', $group->getIcon());
        $this->assertCount(1, $group->getChildren());
    }

    #[Test]
    public function itDefaultsRouteAndRoleToNull(): void
    {
        $group = MenuItemGroup::make()->label('Content');

        $this->assertNull($group->getRoute());
        $this->assertNull($group->getRole());
    }

    #[Test]
    public function itNeverMatchesRoute(): void
    {
        $group = MenuItemGroup::make()->label('Content');

        $this->assertFalse($group->matchesRoute('beacon_admin.dashboard'));
        $this->assertFalse($group->matchesRoute('beacon_admin.content.articles'));
    }
}
