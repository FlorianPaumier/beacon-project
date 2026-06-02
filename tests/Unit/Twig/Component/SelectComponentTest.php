<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Twig\Component;

use Devgeek\BeaconAdmin\Twig\Component\SelectComponent;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

final class SelectComponentTest extends TestCase
{
    #[Test]
    public function itCanBeInstantiated(): void
    {
        $component = new SelectComponent();

        $this->assertInstanceOf(SelectComponent::class, $component);
    }

    #[Test]
    public function itHasAsLiveComponentAttribute(): void
    {
        $reflection = new \ReflectionClass(SelectComponent::class);
        $attributes = $reflection->getAttributes(AsLiveComponent::class);

        $this->assertCount(1, $attributes);
        $this->assertSame('BeaconAdmin:Select', $attributes[0]->newInstance()->serviceConfig()['key']);
    }

    #[Test]
    public function itUsesDefaultActionTrait(): void
    {
        $traits = (new \ReflectionClass(SelectComponent::class))->getTraits();

        $this->assertArrayHasKey(DefaultActionTrait::class, $traits);
    }

    #[Test]
    public function itHasDefaultValues(): void
    {
        $component = new SelectComponent();

        $this->assertSame('', $component->name);
        $this->assertNull($component->label);
        $this->assertFalse($component->required);
        $this->assertSame('', $component->value);
        $this->assertSame([], $component->options);
        $this->assertFalse($component->multiple);
        $this->assertFalse($component->searchable);
    }

    #[Test]
    public function itHasRequiredLiveProps(): void
    {
        $this->assertLivePropIsWritable('name');
        $this->assertLivePropIsWritable('value');
        $this->assertLivePropExists('label');
        $this->assertLivePropExists('required');
        $this->assertLivePropExists('options');
        $this->assertLivePropExists('multiple');
        $this->assertLivePropExists('searchable');
    }

    private function assertLivePropExists(string $property): void
    {
        $reflection = new \ReflectionProperty(SelectComponent::class, $property);
        $attributes = $reflection->getAttributes(LiveProp::class);

        $this->assertCount(1, $attributes, sprintf('Property $%s is missing #[LiveProp] attribute', $property));
    }

    private function assertLivePropIsWritable(string $property): void
    {
        $reflection = new \ReflectionProperty(SelectComponent::class, $property);
        $attributes = $reflection->getAttributes(LiveProp::class);

        $this->assertCount(1, $attributes, sprintf('Property $%s is missing #[LiveProp] attribute', $property));

        $this->assertTrue(
            $attributes[0]->newInstance()->isIdentityWritable(),
            sprintf('Property $%s is not writable', $property),
        );
    }
}
