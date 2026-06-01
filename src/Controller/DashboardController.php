<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Controller;

use Devgeek\BeaconAdmin\Widget\WidgetRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DashboardController extends AbstractController
{
    #[Route('/%beacon_admin.route_prefix%', name: 'beacon_admin.dashboard')]
    public function __construct(
        private readonly WidgetRegistry $widgets,
    ) {
    }

    public function __invoke(): Response
    {
        return $this->render('@BeaconAdmin/dashboard.html.twig', [
            'widgets' => $this->widgets->all(),
        ]);
    }
}
