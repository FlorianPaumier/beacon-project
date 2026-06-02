<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Twig\Component;

use Devgeek\BeaconAdmin\Twig\Component\RadioComponent;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

final class RadioComponentTest extends TestCase
{
    #[Test]
    public function itCanBeInstantiated(): void
    {
        $this->assertInstanceOf(RadioComponent::class, new RadioComponent());
    }

    #[Test]
    public function itHasAsLiveComponentAttribute(): void
    {
        $this->assertSame('BeaconAdmin:Radio', (new \ReflectionClass(RadioComponent::class))->getAttributes(AsLiveComponent::class)[0]->newInstance()->serviceConfig()['key']);
    }

    #[Test]
    public function itUsesDefaultActionTrait(): void
    {
        $this->assertArrayHasKey(DefaultActionTrait::class, (new \ReflectionClass(RadioComponent::class))->getTraits());
    }

    #[Test]
    public function itHasDefaultValues(): void
    {
        $c = new RadioComponent();
        $this->assertSame('', $c->name);
        $this->assertNull($c->label);
        $this->assertSame('', $c->value);
        $this->assertFalse($c->required);
        $this->assertSame([], $c->options);
        $this->assertSame('vertical', $c->layout);
    }

    #[Test]
    public function itHasWritableLiveProps(): void
    {
        foreach (['name', 'value'] as $prop) {
            $this->assertTrue((new \ReflectionProperty(RadioComponent::class, $prop))->getAttributes(LiveProp::class)[0]->newInstance()->isIdentityWritable());
        }
    }

    #[Test]
    public function itHasNonWritableLiveProps(): void
    {
        foreach (['label', 'required', 'options', 'layout'] as $prop) {
            $this->assertCount(1, (new \ReflectionProperty(RadioComponent::class, $prop))->getAttributes(LiveProp::class));
        }
    }
}
