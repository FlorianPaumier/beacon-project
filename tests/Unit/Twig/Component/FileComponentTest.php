<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Twig\Component;

use Devgeek\BeaconAdmin\Twig\Component\FileComponent;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

final class FileComponentTest extends TestCase
{
    #[Test]
    public function itCanBeInstantiated(): void
    {
        $this->assertInstanceOf(FileComponent::class, new FileComponent());
    }

    #[Test]
    public function itHasAsLiveComponentAttribute(): void
    {
        $this->assertSame('BeaconAdmin:File', (new \ReflectionClass(FileComponent::class))->getAttributes(AsLiveComponent::class)[0]->newInstance()->serviceConfig()['key']);
    }

    #[Test]
    public function itUsesDefaultActionTrait(): void
    {
        $this->assertArrayHasKey(DefaultActionTrait::class, (new \ReflectionClass(FileComponent::class))->getTraits());
    }

    #[Test]
    public function itHasDefaultValues(): void
    {
        $c = new FileComponent();
        $this->assertSame('', $c->name);
        $this->assertNull($c->label);
        $this->assertFalse($c->required);
        $this->assertFalse($c->multiple);
        $this->assertNull($c->accept);
        $this->assertNull($c->maxSize);
    }

    #[Test]
    public function itHasWritableLiveProps(): void
    {
        $this->assertTrue((new \ReflectionProperty(FileComponent::class, 'name'))->getAttributes(LiveProp::class)[0]->newInstance()->isIdentityWritable());
    }

    #[Test]
    public function itHasNonWritableLiveProps(): void
    {
        foreach (['label', 'required', 'multiple', 'accept', 'maxSize'] as $prop) {
            $this->assertCount(1, (new \ReflectionProperty(FileComponent::class, $prop))->getAttributes(LiveProp::class));
        }
    }
}
