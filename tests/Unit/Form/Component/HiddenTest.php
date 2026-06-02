<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Form\Component;

use Devgeek\BeaconAdmin\Form\Component\Hidden;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class HiddenTest extends TestCase
{
    #[Test]
    public function itCreatesViaMake(): void
    {
        $input = Hidden::make();

        $this->assertInstanceOf(Hidden::class, $input);
    }

    #[Test]
    public function itSetsNameFluently(): void
    {
        $input = Hidden::make()->name('token');

        $this->assertSame('token', $input->getName());
    }

    #[Test]
    public function itRequiresName(): void
    {
        $input = Hidden::make()->name('csrf_token');

        $this->assertSame('csrf_token', $input->getName());
    }
}
