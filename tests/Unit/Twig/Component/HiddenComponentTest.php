<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Twig\Component;

use Devgeek\BeaconAdmin\Twig\Component\HiddenComponent;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

final class HiddenComponentTest extends TestCase
{
    #[Test]
    public function itCanBeInstantiated(): void
    {
        $c = new HiddenComponent();
        $this->assertSame('', $c->name);
    }

    #[Test]
    public function itHasAsLiveComponentAttribute(): void
    {
        $this->assertSame('BeaconAdmin:Hidden', (new \ReflectionClass(HiddenComponent::class))->getAttributes(AsLiveComponent::class)[0]->newInstance()->serviceConfig()['key']);
    }

    #[Test]
    public function itUsesDefaultActionTrait(): void
    {
        $this->assertArrayHasKey(DefaultActionTrait::class, (new \ReflectionClass(HiddenComponent::class))->getTraits());
    }

    #[Test]
    public function itHasDefaultValues(): void
    {
        $c = new HiddenComponent();
        $this->assertSame('', $c->name);
        $this->assertSame('', $c->value);
    }

    #[Test]
    public function itHasWritableLiveProps(): void
    {
        foreach (['name', 'value'] as $prop) {
            $this->assertTrue((new \ReflectionProperty(HiddenComponent::class, $prop))->getAttributes(LiveProp::class)[0]->newInstance()->isIdentityWritable());
        }
    }
}
