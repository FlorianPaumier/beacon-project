<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Twig\Component;

use Devgeek\BeaconAdmin\Twig\Component\FieldsetComponent;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

final class FieldsetComponentTest extends TestCase
{
    #[Test]
    public function itCanBeInstantiated(): void
    {
        $component = new FieldsetComponent();

        $this->assertInstanceOf(FieldsetComponent::class, $component);
    }

    #[Test]
    public function itHasAsLiveComponentAttribute(): void
    {
        $reflection = new \ReflectionClass(FieldsetComponent::class);
        $attributes = $reflection->getAttributes(AsLiveComponent::class);

        $this->assertCount(1, $attributes);
        $this->assertSame('BeaconAdmin:Fieldset', $attributes[0]->newInstance()->serviceConfig()['key']);
    }

    #[Test]
    public function itUsesDefaultActionTrait(): void
    {
        $traits = (new \ReflectionClass(FieldsetComponent::class))->getTraits();

        $this->assertArrayHasKey(DefaultActionTrait::class, $traits);
    }

    #[Test]
    public function itHasDefaultValues(): void
    {
        $component = new FieldsetComponent();

        $this->assertNull($component->label);
        $this->assertSame([], $component->schema);
    }

    #[Test]
    public function itHasLivePropOnLabel(): void
    {
        $reflection = new \ReflectionProperty(FieldsetComponent::class, 'label');
        $attributes = $reflection->getAttributes(LiveProp::class);

        $this->assertCount(1, $attributes);
    }

    #[Test]
    public function itHasLivePropOnSchema(): void
    {
        $reflection = new \ReflectionProperty(FieldsetComponent::class, 'schema');
        $attributes = $reflection->getAttributes(LiveProp::class);

        $this->assertCount(1, $attributes);
    }
}
