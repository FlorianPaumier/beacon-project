<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Command;

use Devgeek\BeaconAdmin\Command\MakeResourceCommand;
use Devgeek\BeaconAdmin\Crud\Doctrine\EntityIntrospector;
use Devgeek\BeaconAdmin\Crud\Doctrine\EntityMetadata;
use Devgeek\BeaconAdmin\Crud\Doctrine\FieldMetadata;
use Devgeek\BeaconAdmin\Tests\Fixtures\TestApp\Entity\TestEntity;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

final class MakeResourceCommandTest extends TestCase
{
    #[Test]
    public function itFailsWhenEntityDoesNotExist(): void
    {
        $introspector = $this->createMock(EntityIntrospector::class);
        $command = new MakeResourceCommand($introspector);
        $tester = new CommandTester($command);

        $exitCode = $tester->execute(['entity' => 'App\Entity\DoesNotExist']);

        $this->assertSame(1, $exitCode);
        $this->assertStringContainsString('does not exist', $tester->getDisplay());
    }

    #[Test]
    public function itGeneratesController(): void
    {
        $metadata = EntityMetadata::make()
            ->className(TestEntity::class)
            ->tableName('test_entity')
            ->fields([
                FieldMetadata::make()->name('id')->type('integer'),
                FieldMetadata::make()->name('name')->type('string'),
            ]);

        $introspector = $this->createMock(EntityIntrospector::class);
        $introspector->method('introspectFromDefault')
            ->with(TestEntity::class)
            ->willReturn($metadata);

        $command = new MakeResourceCommand($introspector);
        $tester = new CommandTester($command);

        $tempDir = sys_get_temp_dir().'/beacon-test-'.uniqid();
        mkdir($tempDir, 0o755, true);

        try {
            $exitCode = $tester->execute([
                'entity' => TestEntity::class,
                '--output-dir' => $tempDir,
            ]);

            $this->assertSame(0, $exitCode);
            $this->assertFileExists($tempDir.'/TestEntityCrudController.php');

            $content = file_get_contents($tempDir.'/TestEntityCrudController.php');
            $this->assertStringContainsString('class TestEntityCrudController extends AbstractCrudController', $content);
            $this->assertStringContainsString("'id'", $content);
            $this->assertStringContainsString("'name'", $content);
        } finally {
            if (file_exists($tempDir.'/TestEntityCrudController.php')) {
                unlink($tempDir.'/TestEntityCrudController.php');
            }
            rmdir($tempDir);
        }
    }

    #[Test]
    public function itDryRunsWithoutWriting(): void
    {
        $metadata = EntityMetadata::make()
            ->className(TestEntity::class)
            ->tableName('test_entity')
            ->fields([FieldMetadata::make()->name('id')->type('integer')]);

        $introspector = $this->createMock(EntityIntrospector::class);
        $introspector->method('introspectFromDefault')->willReturn($metadata);

        $command = new MakeResourceCommand($introspector);
        $tester = new CommandTester($command);

        $tempDir = sys_get_temp_dir().'/beacon-test-'.uniqid();
        $exitCode = $tester->execute([
            'entity' => TestEntity::class,
            '--output-dir' => $tempDir,
            '--dry-run' => true,
        ]);

        $this->assertSame(0, $exitCode);
        $this->assertStringContainsString('Generated code', $tester->getDisplay());
        $this->assertFileDoesNotExist($tempDir.'/TestEntityCrudController.php');
    }

    #[Test]
    public function itRejectsExistingFile(): void
    {
        $metadata = EntityMetadata::make()
            ->className(TestEntity::class)
            ->tableName('test_entity')
            ->fields([FieldMetadata::make()->name('id')->type('integer')]);

        $introspector = $this->createMock(EntityIntrospector::class);
        $introspector->method('introspectFromDefault')->willReturn($metadata);

        $command = new MakeResourceCommand($introspector);
        $tester = new CommandTester($command);

        $tempDir = sys_get_temp_dir().'/beacon-test-'.uniqid();
        mkdir($tempDir, 0o755, true);
        file_put_contents($tempDir.'/TestEntityCrudController.php', 'existing');

        try {
            $exitCode = $tester->execute([
                'entity' => TestEntity::class,
                '--output-dir' => $tempDir,
            ]);

            $this->assertSame(1, $exitCode);
            $this->assertStringContainsString('already exists', $tester->getDisplay());
        } finally {
            unlink($tempDir.'/TestEntityCrudController.php');
            rmdir($tempDir);
        }
    }
}
