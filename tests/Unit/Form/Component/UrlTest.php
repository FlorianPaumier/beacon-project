<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Form\Component;

use Devgeek\BeaconAdmin\Form\Component\Url;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class UrlTest extends TestCase
{
    #[Test]
    public function itCreatesViaMake(): void
    {
        $input = Url::make();

        $this->assertNotNull($input);
    }

    #[Test]
    public function itSetsNameFluently(): void
    {
        $input = Url::make()->name('website');

        $this->assertSame('website', $input->getName());
    }

    #[Test]
    public function itSetsLabelFluently(): void
    {
        $input = Url::make()->name('website')->label('Website');

        $this->assertSame('Website', $input->getLabel());
    }

    #[Test]
    public function itEvaluatesClosureLabel(): void
    {
        $input = Url::make()->name('website')->label(fn () => 'Company URL');

        $this->assertSame('Company URL', $input->getLabel());
    }

    #[Test]
    public function itDefaultsRequiredToFalse(): void
    {
        $input = Url::make()->name('website');

        $this->assertFalse($input->isRequired());
    }

    #[Test]
    public function itSetsRequiredFluently(): void
    {
        $input = Url::make()->name('website')->required();

        $this->assertTrue($input->isRequired());
    }

    #[Test]
    public function itEvaluatesClosureRequired(): void
    {
        $input = Url::make()->name('website')->required(fn () => true);

        $this->assertTrue($input->isRequired());
    }

    #[Test]
    public function itReturnsNullPlaceholderByDefault(): void
    {
        $input = Url::make()->name('website');

        $this->assertNull($input->getPlaceholder());
    }

    #[Test]
    public function itSetsPlaceholderFluently(): void
    {
        $input = Url::make()->name('website')->placeholder('https://example.com');

        $this->assertSame('https://example.com', $input->getPlaceholder());
    }

    #[Test]
    public function itEvaluatesClosurePlaceholder(): void
    {
        $input = Url::make()->name('website')->placeholder(fn () => 'Enter URL');

        $this->assertSame('Enter URL', $input->getPlaceholder());
    }

    #[Test]
    public function itChainsAllSetters(): void
    {
        $input = Url::make()
            ->name('portfolio')
            ->label('Portfolio URL')
            ->required()
            ->placeholder('https://my.site');

        $this->assertSame('portfolio', $input->getName());
        $this->assertSame('Portfolio URL', $input->getLabel());
        $this->assertTrue($input->isRequired());
        $this->assertSame('https://my.site', $input->getPlaceholder());
    }
}
