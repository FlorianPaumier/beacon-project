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
            ->addOption('route-prefix', null, InputOption::VALUE_OPTIONAL, 'Route prefix (e.g. /admin/users)', null)
            ->addOption('route-name', null, InputOption::VALUE_OPTIONAL, 'Route name prefix (e.g. admin_user)', null)
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

        $routePrefix = $input->getOption('route-prefix');
        if ($routePrefix === null) {
            $routePrefix = '/admin/'.$this->camelToKebab($shortName);
        }

        $routeName = $input->getOption('route-name');
        if ($routeName === null) {
            $routeName = 'admin_'.$this->camelToSnake($shortName);
        }

        $code = $this->generateController($controllerClass, $entityClass, $metadata, $routePrefix, $routeName);

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

    private function camelToKebab(string $string): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $string));
    }

    private function camelToSnake(string $string): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $string));
    }

    private function pluralize(string $singular): string
    {
        if (preg_match('/[sxz]|ch|sh$/i', $singular)) {
            return $singular.'es';
        }

        if (preg_match('/[^aeiou]y$/i', $singular)) {
            return substr($singular, 0, -1).'ies';
        }

        return $singular.'s';
    }

    protected function getSkeletonPath(): string
    {
        return __DIR__.'/../Resources/skeleton/crud-controller.php.skeleton';
    }

    /** @param array<string, string> $variables */
    protected function renderSkeleton(array $variables): string
    {
        $skeletonPath = $this->getSkeletonPath();

        if (!file_exists($skeletonPath)) {
            throw new \RuntimeException(sprintf('Skeleton file not found at "%s".', $skeletonPath));
        }

        $skeleton = file_get_contents($skeletonPath);

        if ($variables === []) {
            return $skeleton;
        }

        $pattern = '/(?:' . implode('|', array_map(static fn (string $key): string => preg_quote($key, '/'), array_keys($variables))) . ')/';

        return preg_replace_callback(
            $pattern,
            static fn (array $match): string => $variables[$match[0]] ?? $match[0],
            $skeleton,
        ) ?? $skeleton;
    }

    private function generateController(
        string $controllerClass,
        string $entityClass,
        \Devgeek\BeaconAdmin\Crud\Doctrine\EntityMetadata $metadata,
        string $routePrefix,
        string $routeName,
    ): string {
        $namespace = 'App\\Controller\\Admin';
        $shortName = $this->getShortName($entityClass);
        $fieldList = $metadata->getFieldNames();

        $stringFields = [];
        foreach ($fieldList as $field) {
            $stringFields[] = "'".$field."'";
        }
        $fieldsCode = implode(', ', $stringFields);

        return $this->renderSkeleton([
            '{namespace}' => $namespace,
            '{entity_class}' => $entityClass,
            '{controller_class}' => $controllerClass,
            '{route_prefix}' => $routePrefix,
            '{route_name}' => $routeName,
            '{entity_label}' => $shortName,
            '{entity_label_plural}' => $this->pluralize($shortName),
            '{fields}' => $fieldsCode,
            '{page_size}' => '25',
        ]);
    }
}
