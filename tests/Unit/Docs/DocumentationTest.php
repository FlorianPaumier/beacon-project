<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Docs;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class DocumentationTest extends TestCase
{
    private string $docsDir;

    protected function setUp(): void
    {
        $this->docsDir = __DIR__.'/../../../docs';
    }

    #[Test]
    public function gettingStartedDocExists(): void
    {
        $this->assertFileExists($this->docsDir.'/getting-started.md');
    }

    #[Test]
    public function configurationDocExists(): void
    {
        $this->assertFileExists($this->docsDir.'/configuration.md');
    }

    #[Test]
    public function crudDocExists(): void
    {
        $this->assertFileExists($this->docsDir.'/crud.md');
    }

    #[Test]
    public function widgetsDocExists(): void
    {
        $this->assertFileExists($this->docsDir.'/widgets.md');
    }

    #[Test]
    public function themingDocExists(): void
    {
        $this->assertFileExists($this->docsDir.'/theming.md');
    }

    #[Test]
    public function allFiveDocsPresent(): void
    {
        $expected = [
            'getting-started.md',
            'configuration.md',
            'crud.md',
            'widgets.md',
            'theming.md',
        ];

        foreach ($expected as $doc) {
            $this->assertFileExists($this->docsDir.'/'.$doc);
        }
    }

    #[Test]
    public function noOtherMarkdownFilesInDocs(): void
    {
        $files = glob($this->docsDir.'/*.md');
        $expected = 5;
        $this->assertCount($expected, $files, sprintf('Expected %d markdown files in docs/', $expected));
    }
}
