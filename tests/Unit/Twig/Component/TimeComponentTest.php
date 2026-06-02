<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Twig\Component;

use Devgeek\BeaconAdmin\Twig\Component\TimeComponent;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

final class TimeComponentTest extends TestCase
{
    #[Test]
    public function itCanBeInstantiated(): void
    {
        $c = new TimeComponent();
        $this->assertSame('', $c->name);
    }

    #[Test]
    public function itHasAsLiveComponentAttribute(): void
    {
        $this->assertSame('BeaconAdmin:Time', (new \ReflectionClass(TimeComponent::class))->getAttributes(AsLiveComponent::class)[0]->newInstance()->serviceConfig()['key']);
    }

    #[Test]
    public function itUsesDefaultActionTrait(): void
    {
        $this->assertArrayHasKey(DefaultActionTrait::class, (new \ReflectionClass(TimeComponent::class))->getTraits());
    }

    #[Test]
    public function itHasDefaultValues(): void
    {
        $c = new TimeComponent();
        $this->assertSame('', $c->name);
        $this->assertNull($c->label);
        $this->assertSame('', $c->value);
        $this->assertFalse($c->required);
    }

    #[Test]
    public function itHasWritableLiveProps(): void
    {
        foreach (['name', 'value'] as $prop) {
            $this->assertTrue((new \ReflectionProperty(TimeComponent::class, $prop))->getAttributes(LiveProp::class)[0]->newInstance()->isIdentityWritable());
        }
    }

    #[Test]
    public function itHasNonWritableLiveProps(): void
    {
        foreach (['label', 'required'] as $prop) {
            $this->assertCount(1, (new \ReflectionProperty(TimeComponent::class, $prop))->getAttributes(LiveProp::class));
        }
    }
}
