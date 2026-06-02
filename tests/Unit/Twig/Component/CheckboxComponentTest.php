<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Twig\Component;

use Devgeek\BeaconAdmin\Twig\Component\CheckboxComponent;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

final class CheckboxComponentTest extends TestCase
{
    #[Test]
    public function itCanBeInstantiated(): void
    {
        $component = new CheckboxComponent();

        $this->assertFalse($component->default);
    }

    #[Test]
    public function itHasAsLiveComponentAttribute(): void
    {
        $reflection = new \ReflectionClass(CheckboxComponent::class);
        $attributes = $reflection->getAttributes(AsLiveComponent::class);

        $this->assertCount(1, $attributes);
        $this->assertSame('BeaconAdmin:Checkbox', $attributes[0]->newInstance()->serviceConfig()['key']);
    }

    #[Test]
    public function itUsesDefaultActionTrait(): void
    {
        $traits = (new \ReflectionClass(CheckboxComponent::class))->getTraits();

        $this->assertArrayHasKey(DefaultActionTrait::class, $traits);
    }

    #[Test]
    public function itHasDefaultValues(): void
    {
        $component = new CheckboxComponent();

        $this->assertSame('', $component->name);
        $this->assertNull($component->label);
        $this->assertFalse($component->default);
    }

    #[Test]
    public function itHasRequiredLiveProps(): void
    {
        $this->assertLivePropIsWritable('name');
        $this->assertLivePropIsWritable('default');
        $this->assertLivePropExists('label');
    }

    private function assertLivePropExists(string $property): void
    {
        $reflection = new \ReflectionProperty(CheckboxComponent::class, $property);
        $attributes = $reflection->getAttributes(LiveProp::class);

        $this->assertCount(1, $attributes, sprintf('Property $%s is missing #[LiveProp] attribute', $property));
    }

    private function assertLivePropIsWritable(string $property): void
    {
        $reflection = new \ReflectionProperty(CheckboxComponent::class, $property);
        $attributes = $reflection->getAttributes(LiveProp::class);

        $this->assertCount(1, $attributes, sprintf('Property $%s is missing #[LiveProp] attribute', $property));

        $this->assertTrue(
            $attributes[0]->newInstance()->isIdentityWritable(),
            sprintf('Property $%s is not writable', $property),
        );
    }
}
