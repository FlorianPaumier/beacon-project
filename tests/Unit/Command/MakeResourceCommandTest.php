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
    private const FIXTURE_SKELETON = __DIR__.'/../../Fixtures/skeleton/crud-controller.php.skeleton';

    private function createCommand(EntityIntrospector $introspector, string $skeletonPath): MakeResourceCommand
    {
        return new class($introspector, $skeletonPath) extends MakeResourceCommand {
            public function __construct(
                EntityIntrospector $introspector,
                private readonly string $skeletonPath,
            ) {
                parent::__construct($introspector);
            }

            protected function getSkeletonPath(): string
            {
                return $this->skeletonPath;
            }
        };
    }

    #[Test]
    public function itFailsWhenEntityDoesNotExist(): void
    {
        $introspector = $this->createMock(EntityIntrospector::class);
        $command = $this->createCommand($introspector, self::FIXTURE_SKELETON);
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

        $command = $this->createCommand($introspector, self::FIXTURE_SKELETON);
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

        $command = $this->createCommand($introspector, self::FIXTURE_SKELETON);
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

        $command = $this->createCommand($introspector, self::FIXTURE_SKELETON);
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

    #[Test]
    public function itResolvesDefaultSkeletonRelativePath(): void
    {
        $introspector = $this->createMock(EntityIntrospector::class);
        $command = new MakeResourceCommand($introspector);

        $reflection = new \ReflectionMethod($command, 'getSkeletonPath');
        $path = $reflection->invoke($command);

        $this->assertFileExists($path);
        $this->assertStringContainsString(
            'Resources/skeleton/crud-controller.php.skeleton',
            $path,
        );
    }

    #[Test]
    public function itThrowsExceptionWhenSkeletonMissing(): void
    {
        $metadata = EntityMetadata::make()
            ->className(TestEntity::class)
            ->tableName('test_entity')
            ->fields([FieldMetadata::make()->name('id')->type('integer')]);

        $introspector = $this->createMock(EntityIntrospector::class);
        $introspector->method('introspectFromDefault')->willReturn($metadata);

        $command = $this->createCommand(
            $introspector,
            '/dev/null/nonexistent-skeleton.php.skeleton',
        );
        $tester = new CommandTester($command);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Skeleton file not found');

        $tester->execute([
            'entity' => TestEntity::class,
            '--dry-run' => true,
        ]);
    }

    #[Test]
    public function itDoesNotReSubstituteValuesContainingPlaceholderTokens(): void
    {
        $introspector = $this->createMock(EntityIntrospector::class);

        $tempDir = sys_get_temp_dir().'/beacon-test-skel-'.uniqid();
        mkdir($tempDir, 0o755, true);

        $skeletonPath = $tempDir.'/trap.skeleton';
        file_put_contents(
            $skeletonPath,
            "namespace {namespace};\nuse {entity_class};\nclass {controller_class}\n{\n    public const MARKER = '{marker}';\n}\n",
        );

        try {
            $command = $this->createCommand($introspector, $skeletonPath);

            $reflection = new \ReflectionMethod($command, 'renderSkeleton');

            $output = $reflection->invoke($command, [
                '{namespace}' => 'App\\Foo',
                '{entity_class}' => 'App\\Entity\\User',
                '{controller_class}' => 'UserCrudController',
                '{marker}' => '{controller_class}',
            ]);

            $this->assertStringContainsString('namespace App\\Foo;', $output);
            $this->assertStringContainsString('use App\\Entity\\User;', $output);
            $this->assertStringContainsString('class UserCrudController', $output);
            $this->assertStringContainsString("public const MARKER = '{controller_class}';", $output);
        } finally {
            unlink($skeletonPath);
            rmdir($tempDir);
        }
    }
}
