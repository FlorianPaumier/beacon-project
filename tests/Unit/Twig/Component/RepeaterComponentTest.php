<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Twig\Component;

use Devgeek\BeaconAdmin\Twig\Component\RepeaterComponent;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

final class RepeaterComponentTest extends TestCase
{
    #[Test]
    public function itCanBeInstantiated(): void
    {
        $c = new RepeaterComponent();
        $this->assertSame('', $c->name);
    }

    #[Test]
    public function itHasAsLiveComponentAttribute(): void
    {
        $this->assertSame('BeaconAdmin:Repeater', (new \ReflectionClass(RepeaterComponent::class))->getAttributes(AsLiveComponent::class)[0]->newInstance()->serviceConfig()['key']);
    }

    #[Test]
    public function itUsesDefaultActionTrait(): void
    {
        $this->assertArrayHasKey(DefaultActionTrait::class, (new \ReflectionClass(RepeaterComponent::class))->getTraits());
    }

    #[Test]
    public function itHasDefaultValues(): void
    {
        $c = new RepeaterComponent();
        $this->assertSame('', $c->name);
        $this->assertNull($c->label);
        $this->assertSame([], $c->items);
        $this->assertSame([], $c->schema);
        $this->assertNull($c->minItems);
        $this->assertNull($c->maxItems);
        $this->assertSame('Add', $c->addLabel);
    }

    #[Test]
    public function itHasWritableLiveProps(): void
    {
        foreach (['name', 'items'] as $prop) {
            $this->assertTrue((new \ReflectionProperty(RepeaterComponent::class, $prop))->getAttributes(LiveProp::class)[0]->newInstance()->isIdentityWritable());
        }
    }

    #[Test]
    public function itHasNonWritableLiveProps(): void
    {
        foreach (['label', 'schema', 'minItems', 'maxItems', 'addLabel'] as $prop) {
            $this->assertCount(1, (new \ReflectionProperty(RepeaterComponent::class, $prop))->getAttributes(LiveProp::class));
        }
    }
}
