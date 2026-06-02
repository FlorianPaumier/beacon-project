<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Crud\DataTable;

use Devgeek\BeaconAdmin\Crud\CrudConfig;
use Twig\Environment;

class DataTableRenderer
{
    public function __construct(
        protected Environment $twig,
    ) {
    }

    public function render(DataTableResult $result, CrudConfig $config): string
    {
        return $this->twig->render('@BeaconAdmin/crud/list.html.twig', [
            'result' => $result,
            'config' => $config,
        ]);
    }
}
