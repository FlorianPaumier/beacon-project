<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Command;

use Devgeek\BeaconAdmin\Crud\Doctrine\EntityIntrospector;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'make:beacon-resource',
    description: 'Creates a Beacon Admin CRUD controller for a Doctrine entity',
)]
class MakeResourceCommand extends Command
{
    public function __construct(
        private readonly EntityIntrospector $introspector,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('entity', InputArgument::REQUIRED, 'The entity FQCN (e.g. App\\Entity\\User)')
            ->addOption('output-dir', 'o', InputOption::VALUE_OPTIONAL, 'Output directory for the controller', 'src/Controller/Admin')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Show what would be generated without writing files');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        /** @var string $entityClass */
        $entityClass = $input->getArgument('entity');

        if (!class_exists($entityClass)) {
            $io->error(sprintf('Entity class "%s" does not exist.', $entityClass));

            return Command::FAILURE;
        }

        $metadata = $this->introspector->introspectFromDefault($entityClass);
        $shortName = $this->getShortName($entityClass);
        $outputDir = (string) $input->getOption('output-dir');
        $controllerClass = $shortName.'CrudController';
        $filePath = $outputDir.'/'.$controllerClass.'.php';

        $code = $this->generateController($controllerClass, $entityClass, $metadata);

        if ((bool) $input->getOption('dry-run')) {
            $io->section('Generated code (dry run)');
            $io->text($code);

            return Command::SUCCESS;
        }

        if (!is_dir($outputDir) && !mkdir($outputDir, 0o755, true) && !is_dir($outputDir)) {
            $io->error(sprintf('Could not create output directory: %s', $outputDir));

            return Command::FAILURE;
        }

        if (file_exists($filePath)) {
            $io->error(sprintf('Controller "%s" already exists at "%s".', $controllerClass, $filePath));

            return Command::FAILURE;
        }

        file_put_contents($filePath, $code);

        $io->success(sprintf('Created %s at %s', $controllerClass, $filePath));

        return Command::SUCCESS;
    }

    private function getShortName(string $entityClass): string
    {
        $parts = explode('\\', $entityClass);

        return end($parts);
    }

    private function generateController(string $controllerClass, string $entityClass, \Devgeek\BeaconAdmin\Crud\Doctrine\EntityMetadata $metadata): string
    {
        $namespace = 'App\\Controller\\Admin';
        $fieldList = $metadata->getFieldNames();
        $fieldsCode = implode("',\n            '", $fieldList);
        $searchableFields = implode("',\n            '", $fieldList);

        return <<<PHP
            <?php

            declare(strict_types=1);

            namespace {$namespace};

            use Devgeek\\BeaconAdmin\\Controller\\AbstractCrudController;
            use Devgeek\\BeaconAdmin\\Crud\\CrudConfig;
            use {$entityClass};

            class {$controllerClass} extends AbstractCrudController
            {
                protected function configureCrud(CrudConfig \$config): void
                {
                    \$config
                        ->fields([
                            '{$fieldsCode}',
                        ])
                        ->sortableFields([
                            '{$fieldsCode}',
                        ])
                        ->searchableFields([
                            '{$searchableFields}',
                        ])
                        ->pageSize(25);
                }

                protected function getEntityClass(): string
                {
                    return \\{$entityClass}::class;
                }
            }
            PHP;
    }
}
