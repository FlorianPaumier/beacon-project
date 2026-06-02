<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Twig\Component;

use Devgeek\BeaconAdmin\Twig\Component\DateTimeComponent;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

final class DateTimeComponentTest extends TestCase
{
    #[Test]
    public function itCanBeInstantiated(): void
    {
        $this->assertInstanceOf(DateTimeComponent::class, new DateTimeComponent());
    }

    #[Test]
    public function itHasAsLiveComponentAttribute(): void
    {
        $this->assertSame('BeaconAdmin:DateTime', (new \ReflectionClass(DateTimeComponent::class))->getAttributes(AsLiveComponent::class)[0]->newInstance()->serviceConfig()['key']);
    }

    #[Test]
    public function itUsesDefaultActionTrait(): void
    {
        $this->assertArrayHasKey(DefaultActionTrait::class, (new \ReflectionClass(DateTimeComponent::class))->getTraits());
    }

    #[Test]
    public function itHasDefaultValues(): void
    {
        $c = new DateTimeComponent();
        $this->assertSame('', $c->name);
        $this->assertNull($c->label);
        $this->assertSame('', $c->value);
        $this->assertFalse($c->required);
        $this->assertNull($c->min);
        $this->assertNull($c->max);
    }

    #[Test]
    public function itHasWritableLiveProps(): void
    {
        foreach (['name', 'value'] as $prop) {
            $this->assertTrue((new \ReflectionProperty(DateTimeComponent::class, $prop))->getAttributes(LiveProp::class)[0]->newInstance()->isIdentityWritable());
        }
    }

    #[Test]
    public function itHasNonWritableLiveProps(): void
    {
        foreach (['label', 'required', 'min', 'max'] as $prop) {
            $this->assertCount(1, (new \ReflectionProperty(DateTimeComponent::class, $prop))->getAttributes(LiveProp::class));
        }
    }
}
