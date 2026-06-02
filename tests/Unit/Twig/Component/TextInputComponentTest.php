<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Twig\Component;

use Devgeek\BeaconAdmin\Twig\Component\TextInputComponent;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

final class TextInputComponentTest extends TestCase
{
    #[Test]
    public function itCanBeInstantiated(): void
    {
        $component = new TextInputComponent();

        $this->assertInstanceOf(TextInputComponent::class, $component);
    }

    #[Test]
    public function itHasAsLiveComponentAttribute(): void
    {
        $reflection = new \ReflectionClass(TextInputComponent::class);
        $attributes = $reflection->getAttributes(AsLiveComponent::class);

        $this->assertCount(1, $attributes);
        $this->assertSame('BeaconAdmin:TextInput', $attributes[0]->newInstance()->serviceConfig()['key']);
    }

    #[Test]
    public function itUsesDefaultActionTrait(): void
    {
        $traits = (new \ReflectionClass(TextInputComponent::class))->getTraits();

        $this->assertArrayHasKey(DefaultActionTrait::class, $traits);
    }

    #[Test]
    public function itHasDefaultValues(): void
    {
        $component = new TextInputComponent();

        $this->assertSame('', $component->name);
        $this->assertNull($component->label);
        $this->assertFalse($component->required);
        $this->assertFalse($component->email);
        $this->assertNull($component->maxLength);
        $this->assertNull($component->placeholder);
        $this->assertSame('', $component->value);
    }

    #[Test]
    public function itHasLivePropOnName(): void
    {
        $this->assertLivePropIsWritable(TextInputComponent::class, 'name');
    }

    #[Test]
    public function itHasLivePropOnValue(): void
    {
        $this->assertLivePropIsWritable(TextInputComponent::class, 'value');
    }

    #[Test]
    public function itHasLivePropOnLabel(): void
    {
        $this->assertLivePropExists(TextInputComponent::class, 'label');
    }

    #[Test]
    public function itHasLivePropOnRequired(): void
    {
        $this->assertLivePropExists(TextInputComponent::class, 'required');
    }

    #[Test]
    public function itHasLivePropOnEmail(): void
    {
        $this->assertLivePropExists(TextInputComponent::class, 'email');
    }

    #[Test]
    public function itHasLivePropOnMaxLength(): void
    {
        $this->assertLivePropExists(TextInputComponent::class, 'maxLength');
    }

    #[Test]
    public function itHasLivePropOnPlaceholder(): void
    {
        $this->assertLivePropExists(TextInputComponent::class, 'placeholder');
    }

    private function assertLivePropExists(string $class, string $property): void
    {
        $reflection = new \ReflectionProperty($class, $property);
        $attributes = $reflection->getAttributes(LiveProp::class);

        $this->assertCount(1, $attributes, sprintf('Property $%s is missing #[LiveProp] attribute', $property));
    }

    private function assertLivePropIsWritable(string $class, string $property): void
    {
        $reflection = new \ReflectionProperty($class, $property);
        $attributes = $reflection->getAttributes(LiveProp::class);

        $this->assertCount(1, $attributes, sprintf('Property $%s is missing #[LiveProp] attribute', $property));

        $args = $attributes[0]->newInstance();

        $this->assertTrue($args->isIdentityWritable(), sprintf('Property $%s is not writable', $property));
    }
}
