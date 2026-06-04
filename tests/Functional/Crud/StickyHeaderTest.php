<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Functional\Crud;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class StickyHeaderTest extends TestCase
{
    #[Test]
    public function listTemplateHasStickyTheadClass(): void
    {
        $templateSource = file_get_contents(dirname(__DIR__, 3).'/templates/crud/list.html.twig');
        $this->assertNotFalse($templateSource);

        $this->assertStringContainsString('beacon-sticky-header', $templateSource);
    }

    #[Test]
    public function globalStylesheetDefinesStickyPosition(): void
    {
        $css = file_get_contents(dirname(__DIR__, 3).'/assets/styles/beacon-admin.css');
        $this->assertNotFalse($css);

        $this->assertStringContainsString('beacon-sticky-header', $css);
        $this->assertStringContainsString('position: sticky', $css);
        $this->assertStringContainsString('top: 0', $css);
    }

    #[Test]
    public function tableWrapperHasScrollContainerForStickyToWork(): void
    {
        $css = file_get_contents(dirname(__DIR__, 3).'/assets/styles/beacon-admin.css');
        $this->assertNotFalse($css);

        $this->assertStringContainsString('beacon-table-wrapper', $css);
        $this->assertStringContainsString('overflow-y: auto', $css);
        $this->assertStringContainsString('max-height', $css);
    }

    #[Test]
    public function stickyHeaderHasZIndexAndShadow(): void
    {
        $css = file_get_contents(dirname(__DIR__, 3).'/assets/styles/beacon-admin.css');
        $this->assertNotFalse($css);

        $this->assertStringContainsString('z-index: 10', $css);
        $this->assertStringContainsString('box-shadow', $css);
    }
}
