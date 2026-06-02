<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Schema;

use Devgeek\BeaconAdmin\Schema\Schema;
use Devgeek\BeaconAdmin\Support\Component;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class SchemaTest extends TestCase
{
    #[Test]
    public function itAcceptsComponentsViaSchema(): void
    {
        $components = [new SchemaTestComponent(), new SchemaTestComponent()];
        $schema = new Schema();
        $schema->schema($components);

        $this->assertSame($components, $schema->getComponents());
    }

    #[Test]
    public function itDefaultsToEmptyComponents(): void
    {
        $schema = new Schema();

        $this->assertSame([], $schema->getComponents());
    }

    #[Test]
    public function itFillsState(): void
    {
        $state = ['key' => 'value', 'count' => 5];
        $schema = new Schema();
        $schema->fill($state);

        $this->assertSame($state, $schema->getState());
    }

    #[Test]
    public function itDefaultsToEmptyState(): void
    {
        $schema = new Schema();

        $this->assertSame([], $schema->getState());
    }

    #[Test]
    public function itReturnsSelfFromFluentMethods(): void
    {
        $schema = new Schema();

        $this->assertSame($schema, $schema->schema([]));
        $this->assertSame($schema, $schema->fill([]));
    }
}

final class SchemaTestComponent extends Component
{
}
