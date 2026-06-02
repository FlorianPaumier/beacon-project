<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Support;

use Devgeek\BeaconAdmin\Support\Component;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ComponentTest extends TestCase
{
    #[Test]
    public function itCreatesInstanceViaMake(): void
    {
        $instance = TestComponent::make();

        $this->assertInstanceOf(TestComponent::class, $instance);
    }

    #[Test]
    public function itReturnsLiteralValueFromEvaluate(): void
    {
        $component = new TestComponent();

        $this->assertSame('foobar', $component->evaluate('foobar'));
        $this->assertSame(42, $component->evaluate(42));
        $this->assertSame([1, 2, 3], $component->evaluate([1, 2, 3]));
    }

    #[Test]
    public function itResolvesClosureFromEvaluate(): void
    {
        $component = new TestComponent();

        $result = $component->evaluate(fn () => 'resolved');

        $this->assertSame('resolved', $result);
    }

    #[Test]
    public function itReturnsNullWhenClosureReturnsNull(): void
    {
        $component = new TestComponent();

        $result = $component->evaluate(fn () => null);

        $this->assertNull($result);
    }
}

final class TestComponent extends Component
{
}
