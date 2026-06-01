<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Menu;

use Devgeek\BeaconAdmin\Menu\MenuBuilder;
use Devgeek\BeaconAdmin\Menu\MenuItem;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

final class MenuBuilderTest extends TestCase
{
    #[Test]
    public function itBuildsEmptyMenu(): void
    {
        $builder = MenuBuilder::make();

        $this->assertSame([], $builder->build());
    }

    #[Test]
    public function itBuildsMenuFromItems(): void
    {
        $items = [
            MenuItem::make()->label('Dashboard')->route('beacon_admin.dashboard'),
            MenuItem::make()->label('Settings')->route('beacon_admin.settings'),
        ];

        $builder = MenuBuilder::make()->items($items);

        $this->assertCount(2, $builder->build());
    }

    #[Test]
    public function itFiltersItemsByRoleWhenCheckerSet(): void
    {
        $checker = $this->createChecker(allowed: ['ROLE_ADMIN']);
        $items = [
            MenuItem::make()->label('Admin')->route('admin')->role('ROLE_ADMIN'),
            MenuItem::make()->label('Public')->route('public'),
        ];

        $builder = MenuBuilder::make()
            ->checker($checker)
            ->items($items);

        $result = $builder->build();
        $this->assertCount(2, $result);
    }

    #[Test]
    public function itRemovesItemsWithUngrantedRole(): void
    {
        $checker = $this->createChecker(allowed: ['ROLE_ADMIN']);
        $items = [
            MenuItem::make()->label('Admin')->route('admin')->role('ROLE_ADMIN'),
            MenuItem::make()->label('Super')->route('super')->role('ROLE_SUPER_ADMIN'),
        ];

        $builder = MenuBuilder::make()
            ->checker($checker)
            ->items($items);

        $result = $builder->build();
        $this->assertCount(1, $result);
        $this->assertSame('Admin', $result[0]->getLabel());
    }

    #[Test]
    public function itKeepsParentWhenChildrenAreAccessible(): void
    {
        $checker = $this->createChecker(allowed: ['ROLE_USER']);
        $items = [
            MenuItem::make()
                ->label('Settings')
                ->children([
                    MenuItem::make()->label('Profile')->route('profile')->role('ROLE_USER'),
                    MenuItem::make()->label('Admin')->route('admin')->role('ROLE_ADMIN'),
                ]),
        ];

        $builder = MenuBuilder::make()
            ->checker($checker)
            ->items($items);

        $result = $builder->build();
        $this->assertCount(1, $result);
    }

    #[Test]
    public function itFiltersMenuWithoutChecker(): void
    {
        $items = [
            MenuItem::make()->label('Admin')->route('admin')->role('ROLE_ADMIN'),
        ];

        $builder = MenuBuilder::make()->items($items);

        // Without a checker, all items pass through
        $this->assertCount(1, $builder->build());
    }

    #[Test]
    public function itAddsExtensions(): void
    {
        $items = [
            MenuItem::make()->label('Dashboard'),
        ];

        $builder = MenuBuilder::make()->items($items);
        $builder->addExtension(function (array $menu): array {
            $menu[] = MenuItem::make()->label('Injected');

            return $menu;
        });

        $result = $builder->build();
        $this->assertCount(2, $result);
        $this->assertSame('Injected', $result[1]->getLabel());
    }

    #[Test]
    public function itSupportsFluentInterface(): void
    {
        $builder = MenuBuilder::make();
        $result = $builder->items([]);

        $this->assertSame($builder, $result);
    }

    /** @param string[] $allowed */
    private function createChecker(array $allowed): AuthorizationCheckerInterface
    {
        return new readonly class($allowed) implements AuthorizationCheckerInterface {
            /** @param string[] $allowed */
            public function __construct(private array $allowed)
            {
            }

            public function isGranted(mixed $attribute, mixed $subject = null, mixed $accessDecision = null): bool
            {
                return \in_array($attribute, $this->allowed, true);
            }
        };
    }
}
