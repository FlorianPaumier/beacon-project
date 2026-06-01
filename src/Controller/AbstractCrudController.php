<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Controller;

use Devgeek\BeaconAdmin\Crud\CrudConfig;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

abstract class AbstractCrudController extends AbstractController
{
    abstract protected function configureCrud(CrudConfig $config): void;

    abstract protected function getEntityClass(): string;

    /**
     * Returns the fully configured CrudConfig for this resource.
     * Called internally by action methods.
     */
    public function getCrudConfig(): CrudConfig
    {
        $config = CrudConfig::make()->entityClass($this->getEntityClass());

        $this->configureCrud($config);

        return $config;
    }
}
