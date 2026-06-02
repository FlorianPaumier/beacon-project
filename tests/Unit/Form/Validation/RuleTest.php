<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Form\Validation;

use Devgeek\BeaconAdmin\Form\Validation\Rule;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class RuleTest extends TestCase
{
    #[Test]
    public function itCreatesViaMake(): void
    {
        $rule = Rule::make('required');

        $this->assertInstanceOf(Rule::class, $rule);
    }

    #[Test]
    public function itSetsRuleViaMake(): void
    {
        $rule = Rule::make('required');

        $this->assertSame('required', $rule->getRule());
    }

    #[Test]
    public function itSetsRuleFluently(): void
    {
        $rule = Rule::make('required')->rule('email');

        $this->assertSame('email', $rule->getRule());
    }

    #[Test]
    public function itSetsMessageFluently(): void
    {
        $rule = Rule::make('required')->message('This field is required');

        $this->assertSame('This field is required', $rule->getMessage());
    }

    #[Test]
    public function itReturnsNullMessageByDefault(): void
    {
        $rule = Rule::make('required');

        $this->assertNull($rule->getMessage());
    }

    #[Test]
    public function itChainsAllSetters(): void
    {
        $rule = Rule::make('required')
            ->rule('email')
            ->message('Must be a valid email');

        $this->assertSame('email', $rule->getRule());
        $this->assertSame('Must be a valid email', $rule->getMessage());
    }
}
