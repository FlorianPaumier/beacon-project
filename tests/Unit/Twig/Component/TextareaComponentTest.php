<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Twig\Component;

use Devgeek\BeaconAdmin\Twig\Component\TextareaComponent;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

final class TextareaComponentTest extends TestCase
{
    #[Test]
    public function itCanBeInstantiated(): void
    {
        $component = new TextareaComponent();

        $this->assertInstanceOf(TextareaComponent::class, $component);
    }

    #[Test]
    public function itHasAsLiveComponentAttribute(): void
    {
        $this->assertSame('BeaconAdmin:Textarea', (new \ReflectionClass(TextareaComponent::class))->getAttributes(AsLiveComponent::class)[0]->newInstance()->serviceConfig()['key']);
    }

    #[Test]
    public function itUsesDefaultActionTrait(): void
    {
        $this->assertArrayHasKey(DefaultActionTrait::class, (new \ReflectionClass(TextareaComponent::class))->getTraits());
    }

    #[Test]
    public function itHasDefaultValues(): void
    {
        $c = new TextareaComponent();
        $this->assertSame('', $c->name);
        $this->assertNull($c->label);
        $this->assertSame('', $c->value);
        $this->assertFalse($c->required);
        $this->assertNull($c->placeholder);
        $this->assertNull($c->maxLength);
        $this->assertSame(3, $c->rows);
        $this->assertFalse($c->autoResize);
    }

    #[Test]
    public function itHasWritableLiveProps(): void
    {
        $this->assertTrue((new \ReflectionProperty(TextareaComponent::class, 'name'))->getAttributes(LiveProp::class)[0]->newInstance()->isIdentityWritable());
        $this->assertTrue((new \ReflectionProperty(TextareaComponent::class, 'value'))->getAttributes(LiveProp::class)[0]->newInstance()->isIdentityWritable());
    }

    #[Test]
    public function itHasNonWritableLiveProps(): void
    {
        foreach (['label', 'required', 'placeholder', 'maxLength', 'rows', 'autoResize'] as $prop) {
            $this->assertCount(1, (new \ReflectionProperty(TextareaComponent::class, $prop))->getAttributes(LiveProp::class), "Property \${$prop} missing #[LiveProp]");
        }
    }
}
