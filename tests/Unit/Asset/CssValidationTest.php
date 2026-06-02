<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Asset;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class CssValidationTest extends TestCase
{
    private string $mainCss;

    protected function setUp(): void
    {
        $this->mainCss = file_get_contents(__DIR__.'/../../../assets/styles/beacon-admin.css');
    }

    #[Test]
    public function mainCssFileExists(): void
    {
        $this->assertFileExists(__DIR__.'/../../../assets/styles/beacon-admin.css');
    }

    #[Test]
    public function mainCssContainsBeaconClassNames(): void
    {
        $this->assertStringContainsString('.beacon-admin-layout', $this->mainCss);
        $this->assertStringContainsString('.beacon-sidebar', $this->mainCss);
        $this->assertStringContainsString('.beacon-header', $this->mainCss);
        $this->assertStringContainsString('.beacon-table', $this->mainCss);
        $this->assertStringContainsString('.beacon-btn', $this->mainCss);
        $this->assertStringContainsString('.beacon-form-input', $this->mainCss);
    }

    #[Test]
    public function mainCssContainsResponsiveMediaQueries(): void
    {
        $this->assertStringContainsString('@media', $this->mainCss);
        $this->assertStringContainsString('max-width: 767px', $this->mainCss);
        $this->assertStringContainsString('min-width: 768px', $this->mainCss);
    }

    #[Test]
    public function mainCssUsesVarForColors(): void
    {
        $this->assertStringContainsString('var(', $this->mainCss);
    }

    #[Test]
    public function themeCssFilesExist(): void
    {
        $this->assertFileExists(__DIR__.'/../../../assets/styles/beacon-modern.css');
        $this->assertFileExists(__DIR__.'/../../../assets/styles/beacon-enterprise.css');
        $this->assertFileExists(__DIR__.'/../../../assets/styles/beacon-brut.css');
    }

    #[Test]
    public function modernCssContainsBothColorSchemes(): void
    {
        $css = file_get_contents(__DIR__.'/../../../assets/styles/beacon-modern.css');
        $this->assertStringContainsString('[data-theme="modern"]', $css);
        $this->assertStringContainsString('[data-theme="modern"].dark', $css);
        $this->assertStringContainsString('--beacon-primary', $css);
    }

    #[Test]
    public function enterpriseCssContainsBothColorSchemes(): void
    {
        $css = file_get_contents(__DIR__.'/../../../assets/styles/beacon-enterprise.css');
        $this->assertStringContainsString('[data-theme="enterprise"]', $css);
        $this->assertStringContainsString('[data-theme="enterprise"].dark', $css);
    }

    #[Test]
    public function brutCssContainsBothColorSchemes(): void
    {
        $css = file_get_contents(__DIR__.'/../../../assets/styles/beacon-brut.css');
        $this->assertStringContainsString('[data-theme="brut"]', $css);
        $this->assertStringContainsString('[data-theme="brut"].dark', $css);
    }
}
