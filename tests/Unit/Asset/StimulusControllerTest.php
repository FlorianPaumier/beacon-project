<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Asset;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class StimulusControllerTest extends TestCase
{
    private string $controllersDir;

    protected function setUp(): void
    {
        $this->controllersDir = __DIR__.'/../../../assets/controllers';
    }

    #[Test]
    public function allControllerFilesExist(): void
    {
        $this->assertFileExists($this->controllersDir.'/datatable_controller.js');
        $this->assertFileExists($this->controllersDir.'/sidebar_controller.js');
        $this->assertFileExists($this->controllersDir.'/theme_controller.js');
        $this->assertFileExists($this->controllersDir.'/notification_controller.js');
        $this->assertFileExists($this->controllersDir.'/delete_confirm_controller.js');
        $this->assertFileExists($this->controllersDir.'/beacon_controller.js');
    }

    #[Test]
    public function datatableControllerExportsStimulusController(): void
    {
        $content = file_get_contents($this->controllersDir.'/datatable_controller.js');
        $this->assertStringContainsString('import { Controller } from "@hotwired/stimulus"', $content);
        $this->assertStringContainsString('export default class extends Controller', $content);
        $this->assertStringContainsString('static targets', $content);
        $this->assertStringContainsString('static values', $content);
    }

    #[Test]
    public function sidebarControllerExportsStimulusController(): void
    {
        $content = file_get_contents($this->controllersDir.'/sidebar_controller.js');
        $this->assertStringContainsString('import { Controller } from "@hotwired/stimulus"', $content);
        $this->assertStringContainsString('export default class extends Controller', $content);
        $this->assertStringContainsString('static values', $content);
    }

    #[Test]
    public function themeControllerExportsStimulusController(): void
    {
        $content = file_get_contents($this->controllersDir.'/theme_controller.js');
        $this->assertStringContainsString('import { Controller } from "@hotwired/stimulus"', $content);
        $this->assertStringContainsString('export default class extends Controller', $content);
        $this->assertStringContainsString('static values', $content);
    }

    #[Test]
    public function notificationControllerExportsStimulusController(): void
    {
        $content = file_get_contents($this->controllersDir.'/notification_controller.js');
        $this->assertStringContainsString('import { Controller } from "@hotwired/stimulus"', $content);
        $this->assertStringContainsString('export default class extends Controller', $content);
        $this->assertStringContainsString('static values', $content);
    }

    #[Test]
    public function deleteConfirmControllerExportsStimulusController(): void
    {
        $content = file_get_contents($this->controllersDir.'/delete_confirm_controller.js');
        $this->assertStringContainsString('import { Controller } from "@hotwired/stimulus"', $content);
        $this->assertStringContainsString('export default class extends Controller', $content);
        $this->assertStringContainsString('static targets', $content);
        $this->assertStringContainsString('static values', $content);
    }

    #[Test]
    public function beaconControllerExportsStimulusController(): void
    {
        $content = file_get_contents($this->controllersDir.'/beacon_controller.js');
        $this->assertStringContainsString('import { Controller } from "@hotwired/stimulus"', $content);
        $this->assertStringContainsString('export default class extends Controller', $content);
        $this->assertStringContainsString('static targets', $content);
    }
}
