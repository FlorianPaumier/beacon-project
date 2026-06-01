<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Controller;

use Devgeek\BeaconAdmin\Security\BeaconAccess;
use Devgeek\BeaconAdmin\Widget\WidgetRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
#[BeaconAccess(role: 'ROLE_ADMIN')]
class DashboardController extends AbstractController
{
    protected WidgetRegistry $widgets;

    public function __construct(WidgetRegistry $widgets)
    {
        $this->widgets = $widgets;
    }

    public static function make(WidgetRegistry $widgets): static
    {
        return new static($widgets);
    }

    #[Route('/%beacon_admin.route_prefix%', name: 'beacon_admin.dashboard')]
    public function __invoke(): Response
    {
        return $this->render('@BeaconAdmin/dashboard.html.twig', [
            'widgets' => $this->widgets->all(),
        ]);
    }
}
