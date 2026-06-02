<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Twig;

use Devgeek\BeaconAdmin\Schema\Schema;
use Devgeek\BeaconAdmin\Support\Component;
use Devgeek\BeaconAdmin\Twig\SchemaExtension;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class SchemaExtensionTest extends TestCase
{
    #[Test]
    public function itRegistersBeaconSchemaFunction(): void
    {
        $extension = new SchemaExtension();

        $functions = $extension->getFunctions();

        $this->assertCount(1, $functions);
        $this->assertSame('beacon_schema', $functions[0]->getName());
    }

    #[Test]
    public function itRendersSchemaComponentsAsTurboFrames(): void
    {
        $schema = new Schema();
        $schema->schema([new SchemaExtTestComponent(), new SchemaExtTestComponent()]);

        $extension = new SchemaExtension();
        $output = $extension->renderSchema($schema);

        $this->assertStringContainsString('<turbo-frame id="beacon-component-0" class="beacon-schema-component">', $output);
        $this->assertStringContainsString('<turbo-frame id="beacon-component-1" class="beacon-schema-component">', $output);
    }

    #[Test]
    public function itRendersEmptyStringForNoComponents(): void
    {
        $schema = new Schema();
        $extension = new SchemaExtension();

        $this->assertSame('', $extension->renderSchema($schema));
    }
}

final class SchemaExtTestComponent extends Component
{
}
