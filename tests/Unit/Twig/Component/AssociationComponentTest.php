<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Twig\Component;

use Devgeek\BeaconAdmin\Twig\Component\AssociationComponent;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

final class AssociationComponentTest extends TestCase
{
    #[Test]
    public function itCanBeInstantiated(): void
    {
        $c = new AssociationComponent();
        $this->assertSame('', $c->name);
    }

    #[Test]
    public function itHasAsLiveComponentAttribute(): void
    {
        $this->assertSame('BeaconAdmin:Association', (new \ReflectionClass(AssociationComponent::class))->getAttributes(AsLiveComponent::class)[0]->newInstance()->serviceConfig()['key']);
    }

    #[Test]
    public function itUsesDefaultActionTrait(): void
    {
        $this->assertArrayHasKey(DefaultActionTrait::class, (new \ReflectionClass(AssociationComponent::class))->getTraits());
    }

    #[Test]
    public function itHasDefaultValues(): void
    {
        $c = new AssociationComponent();
        $this->assertSame('', $c->name);
        $this->assertNull($c->label);
        $this->assertSame('', $c->value);
        $this->assertFalse($c->required);
        $this->assertNull($c->targetEntity);
        $this->assertSame([], $c->options);
        $this->assertFalse($c->multiple);
        $this->assertFalse($c->searchable);
    }

    #[Test]
    public function itHasWritableLiveProps(): void
    {
        foreach (['name', 'value'] as $prop) {
            $this->assertTrue((new \ReflectionProperty(AssociationComponent::class, $prop))->getAttributes(LiveProp::class)[0]->newInstance()->isIdentityWritable());
        }
    }

    #[Test]
    public function itHasNonWritableLiveProps(): void
    {
        foreach (['label', 'required', 'targetEntity', 'options', 'multiple', 'searchable'] as $prop) {
            $this->assertCount(1, (new \ReflectionProperty(AssociationComponent::class, $prop))->getAttributes(LiveProp::class));
        }
    }
}
