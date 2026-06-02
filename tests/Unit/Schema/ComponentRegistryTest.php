<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Schema;

use Devgeek\BeaconAdmin\Schema\ComponentRegistry;
use Devgeek\BeaconAdmin\Support\Component;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ComponentRegistryTest extends TestCase
{
    #[Test]
    public function itRegistersAComponent(): void
    {
        $registry = new ComponentRegistry();
        $registry->register('header', HeaderComponent::class);

        $this->assertSame(HeaderComponent::class, $registry->get('header'));
    }

    #[Test]
    public function itReturnsNullForUnknownName(): void
    {
        $registry = new ComponentRegistry();

        $this->assertNull($registry->get('nonexistent'));
    }

    #[Test]
    public function itListsAllRegisteredComponents(): void
    {
        $registry = new ComponentRegistry();
        $registry->register('header', HeaderComponent::class);
        $registry->register('footer', FooterComponent::class);

        $this->assertSame([
            'header' => HeaderComponent::class,
            'footer' => FooterComponent::class,
        ], $registry->all());
    }

    #[Test]
    public function itOverwritesExistingRegistration(): void
    {
        $registry = new ComponentRegistry();
        $registry->register('header', HeaderComponent::class);
        $registry->register('header', FooterComponent::class);

        $this->assertSame(FooterComponent::class, $registry->get('header'));
        $this->assertCount(1, $registry->all());
    }

    #[Test]
    public function itReturnsSelfFromRegister(): void
    {
        $registry = new ComponentRegistry();
        $result = $registry->register('header', HeaderComponent::class);

        $this->assertSame($registry, $result);
    }
}

final class HeaderComponent extends Component
{
}

final class FooterComponent extends Component
{
}
