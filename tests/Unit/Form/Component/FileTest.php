<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Form\Component;

use Devgeek\BeaconAdmin\Form\Component\File;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class FileTest extends TestCase
{
    #[Test]
    public function itCreatesViaMake(): void
    {
        $input = File::make();

        $this->assertNull($input->getLabel());
    }

    #[Test]
    public function itSetsNameFluently(): void
    {
        $input = File::make()->name('avatar');

        $this->assertSame('avatar', $input->getName());
    }

    #[Test]
    public function itSetsLabelFluently(): void
    {
        $input = File::make()->name('avatar')->label('Avatar');

        $this->assertSame('Avatar', $input->getLabel());
    }

    #[Test]
    public function itEvaluatesClosureLabel(): void
    {
        $input = File::make()->name('avatar')->label(fn () => 'Profile Picture');

        $this->assertSame('Profile Picture', $input->getLabel());
    }

    #[Test]
    public function itDefaultsRequiredToFalse(): void
    {
        $input = File::make()->name('avatar');

        $this->assertFalse($input->isRequired());
    }

    #[Test]
    public function itSetsRequiredFluently(): void
    {
        $input = File::make()->name('avatar')->required();

        $this->assertTrue($input->isRequired());
    }

    #[Test]
    public function itEvaluatesClosureRequired(): void
    {
        $input = File::make()->name('avatar')->required(fn () => true);

        $this->assertTrue($input->isRequired());
    }

    #[Test]
    public function itDefaultsMultipleToFalse(): void
    {
        $input = File::make()->name('avatar');

        $this->assertFalse($input->isMultiple());
    }

    #[Test]
    public function itSetsMultipleFluently(): void
    {
        $input = File::make()->name('gallery')->multiple();

        $this->assertTrue($input->isMultiple());
    }

    #[Test]
    public function itEvaluatesClosureMultiple(): void
    {
        $input = File::make()->name('gallery')->multiple(fn () => true);

        $this->assertTrue($input->isMultiple());
    }

    #[Test]
    public function itReturnsNullAcceptByDefault(): void
    {
        $input = File::make()->name('avatar');

        $this->assertNull($input->getAccept());
    }

    #[Test]
    public function itSetsAcceptFluently(): void
    {
        $input = File::make()->name('avatar')->accept('image/*');

        $this->assertSame('image/*', $input->getAccept());
    }

    #[Test]
    public function itEvaluatesClosureAccept(): void
    {
        $input = File::make()->name('avatar')->accept(fn () => '.pdf,.doc');

        $this->assertSame('.pdf,.doc', $input->getAccept());
    }

    #[Test]
    public function itReturnsNullMaxSizeByDefault(): void
    {
        $input = File::make()->name('avatar');

        $this->assertNull($input->getMaxSize());
    }

    #[Test]
    public function itSetsMaxSizeFluently(): void
    {
        $input = File::make()->name('avatar')->maxSize(2048);

        $this->assertSame(2048, $input->getMaxSize());
    }

    #[Test]
    public function itEvaluatesClosureMaxSize(): void
    {
        $input = File::make()->name('avatar')->maxSize(fn () => 1024);

        $this->assertSame(1024, $input->getMaxSize());
    }

    #[Test]
    public function itChainsAllSetters(): void
    {
        $input = File::make()
            ->name('document')
            ->label('Document')
            ->required()
            ->accept('.pdf')
            ->maxSize(5120);

        $this->assertSame('document', $input->getName());
        $this->assertSame('Document', $input->getLabel());
        $this->assertTrue($input->isRequired());
        $this->assertSame('.pdf', $input->getAccept());
        $this->assertSame(5120, $input->getMaxSize());
    }
}
