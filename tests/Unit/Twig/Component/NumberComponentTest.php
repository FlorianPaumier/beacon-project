<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Twig\Component;

use Devgeek\BeaconAdmin\Twig\Component\NumberComponent;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

final class NumberComponentTest extends TestCase
{
    #[Test]
    public function itCanBeInstantiated(): void
    {
        $this->assertInstanceOf(NumberComponent::class, new NumberComponent());
    }

    #[Test]
    public function itHasAsLiveComponentAttribute(): void
    {
        $this->assertSame('BeaconAdmin:Number', (new \ReflectionClass(NumberComponent::class))->getAttributes(AsLiveComponent::class)[0]->newInstance()->serviceConfig()['key']);
    }

    #[Test]
    public function itUsesDefaultActionTrait(): void
    {
        $this->assertArrayHasKey(DefaultActionTrait::class, (new \ReflectionClass(NumberComponent::class))->getTraits());
    }

    #[Test]
    public function itHasDefaultValues(): void
    {
        $c = new NumberComponent();
        $this->assertSame('', $c->name);
        $this->assertNull($c->label);
        $this->assertNull($c->value);
        $this->assertFalse($c->required);
        $this->assertNull($c->placeholder);
        $this->assertNull($c->min);
        $this->assertNull($c->max);
        $this->assertNull($c->step);
    }

    #[Test]
    public function itHasWritableLiveProps(): void
    {
        foreach (['name', 'value'] as $prop) {
            $this->assertTrue((new \ReflectionProperty(NumberComponent::class, $prop))->getAttributes(LiveProp::class)[0]->newInstance()->isIdentityWritable());
        }
    }

    #[Test]
    public function itHasNonWritableLiveProps(): void
    {
        foreach (['label', 'required', 'placeholder', 'min', 'max', 'step'] as $prop) {
            $this->assertCount(1, (new \ReflectionProperty(NumberComponent::class, $prop))->getAttributes(LiveProp::class));
        }
    }
}
