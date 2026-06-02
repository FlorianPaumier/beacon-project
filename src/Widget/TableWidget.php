<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Widget;

use Psr\Container\ContainerInterface;
use Twig\Environment;

class TableWidget implements DashboardWidgetInterface
{
    protected string $name;
    protected string $label;
    protected string $repository;
    protected string $method = 'findLatest';
    /** @var array<mixed> */
    protected array $params = [];
    protected int $limit = 5;
    /** @var array<int, array{name: string, label?: string}> */
    protected array $columns = [];
    protected int $cols = 6;
    protected int $priority = 0;
    protected Environment $twig;
    protected ContainerInterface $container;

    public function __construct(Environment $twig, ContainerInterface $container)
    {
        $this->twig = $twig;
        $this->container = $container;
    }

    public static function make(Environment $twig, ContainerInterface $container): self
    {
        return new self($twig, $container);
    }

    public function name(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function label(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function repository(string $repository): static
    {
        $this->repository = $repository;

        return $this;
    }

    public function method(string $method): static
    {
        $this->method = $method;

        return $this;
    }

    /** @param array<mixed> $params */
    public function params(array $params): static
    {
        $this->params = $params;

        return $this;
    }

    public function limit(int $limit): static
    {
        $this->limit = $limit;

        return $this;
    }

    /** @param array<int, array{name: string, label?: string}> $columns */
    public function columns(array $columns): static
    {
        $this->columns = $columns;

        return $this;
    }

    public function cols(int $cols): static
    {
        $this->cols = $cols;

        return $this;
    }

    public function priority(int $priority): static
    {
        $this->priority = $priority;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getRepository(): string
    {
        return $this->repository;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    /** @return array<mixed> */
    public function getParams(): array
    {
        return $this->params;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    /** @return array<int, array{name: string, label?: string}> */
    public function getColumns(): array
    {
        return $this->columns;
    }

    public function getCols(): int
    {
        return $this->cols;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function render(): string
    {
        $repositoryService = $this->container->get($this->repository);

        if (!is_object($repositoryService)) {
            throw new \RuntimeException(sprintf('Repository service "%s" must be an object, got "%s".', $this->repository, get_debug_type($repositoryService)));
        }

        if (!method_exists($repositoryService, $this->method)) {
            throw new \RuntimeException(sprintf('Repository service "%s" has no method "%s".', $this->repository, $this->method));
        }

        /** @var callable $callable */
        $callable = [$repositoryService, $this->method];
        $data = \call_user_func_array($callable, $this->params);

        if ($data instanceof \Traversable) {
            $data = iterator_to_array($data);
        }

        if (is_array($data)) {
            $data = array_slice($data, 0, $this->limit);
        }

        return $this->twig->render('@BeaconAdmin/widgets/table.html.twig', [
            'label' => $this->getLabel(),
            'columns' => $this->columns,
            'data' => $data,
            'limit' => $this->limit,
        ]);
    }
}
