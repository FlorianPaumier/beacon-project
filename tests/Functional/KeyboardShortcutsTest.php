<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Functional;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class KeyboardShortcutsTest extends TestCase
{
    #[Test]
    public function controllerFileExistsAndExportsStimulusController(): void
    {
        $path = dirname(__DIR__, 2).'/assets/controllers/keyboard_shortcuts_controller.js';
        $this->assertFileExists($path);

        $content = file_get_contents($path);
        $this->assertStringContainsString('import { Controller } from "@hotwired/stimulus"', $content);
        $this->assertStringContainsString('export default class extends Controller', $content);
    }

    #[Test]
    public function controllerFileDeclaresLazyFetch(): void
    {
        $path = dirname(__DIR__, 2).'/assets/controllers/keyboard_shortcuts_controller.js';
        $content = file_get_contents($path);

        $this->assertStringContainsString("stimulusFetch: 'lazy'", $content);
    }

    #[Test]
    public function controllerFileHandlesExpectedKeys(): void
    {
        $path = dirname(__DIR__, 2).'/assets/controllers/keyboard_shortcuts_controller.js';
        $content = file_get_contents($path);

        $this->assertStringContainsString('case "n"', $content);
        $this->assertStringContainsString('case "/"', $content);
        $this->assertStringContainsString('case "Escape"', $content);
    }

    #[Test]
    public function controllerIgnoresTypingInFormFields(): void
    {
        $path = dirname(__DIR__, 2).'/assets/controllers/keyboard_shortcuts_controller.js';
        $content = file_get_contents($path);

        $this->assertStringContainsString('isTyping', $content);
        $this->assertStringContainsString('INPUT', $content);
        $this->assertStringContainsString('TEXTAREA', $content);
        $this->assertStringContainsString('isContentEditable', $content);
    }

    #[Test]
    public function controllersJsonRegistersKeyboardShortcutsController(): void
    {
        $path = dirname(__DIR__, 2).'/assets/controllers.json';
        $this->assertFileExists($path);

        $json = json_decode(file_get_contents($path), true, 512, \JSON_THROW_ON_ERROR);
        $this->assertArrayHasKey('controllers', $json);
        $this->assertArrayHasKey('beacon-admin--keyboard-shortcuts', $json['controllers']);

        $entry = $json['controllers']['beacon-admin--keyboard-shortcuts'];
        $this->assertTrue($entry['enabled']);
        $this->assertSame('lazy', $entry['fetch']);
    }

    #[Test]
    public function layoutWiresKeyboardShortcutsController(): void
    {
        $path = dirname(__DIR__, 2).'/templates/layout.html.twig';
        $content = file_get_contents($path);

        $this->assertStringContainsString('beacon-admin--keyboard-shortcuts', $content);
    }

    #[Test]
    public function layoutTemplateAttributesTheControllerOnBody(): void
    {
        $path = dirname(__DIR__, 2).'/templates/layout.html.twig';
        $content = file_get_contents($path);

        $this->assertMatchesRegularExpression(
            '/data-controller="[^"]*beacon-admin--keyboard-shortcuts/',
            $content
        );
    }

    #[Test]
    public function listTemplateWiresShortcutAttributesOnButtonsAndInputs(): void
    {
        $list = file_get_contents(dirname(__DIR__, 2).'/templates/crud/list.html.twig');
        $filters = file_get_contents(dirname(__DIR__, 2).'/templates/crud/_filters.html.twig');

        $this->assertStringContainsString('data-beacon-shortcut="new"', $list);
        $this->assertStringContainsString('data-beacon-shortcut="search"', $filters);
    }
}
