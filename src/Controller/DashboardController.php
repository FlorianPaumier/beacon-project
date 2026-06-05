<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Controller;

use Devgeek\BeaconAdmin\Event\DashboardBuiltEvent;
use Devgeek\BeaconAdmin\Security\BeaconAccess;
use Devgeek\BeaconAdmin\Widget\WidgetRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsController]
#[BeaconAccess(role: 'ROLE_ADMIN')]
class DashboardController extends AbstractController
{
    protected WidgetRegistry $widgets;
    protected EventDispatcherInterface $eventDispatcher;

    public function __construct(WidgetRegistry $widgets, EventDispatcherInterface $eventDispatcher)
    {
        $this->widgets = $widgets;
        $this->eventDispatcher = $eventDispatcher;
    }

    public static function make(WidgetRegistry $widgets, EventDispatcherInterface $eventDispatcher): self
    {
        return new self($widgets, $eventDispatcher);
    }

    #[Route('/{_locale}%beacon_admin.route_prefix%', name: 'beacon_admin.dashboard_locale')]
    #[Route('/%beacon_admin.route_prefix%', name: 'beacon_admin.dashboard')]
    public function __invoke(): Response
    {
        $widgets = $this->widgets->all();

        $event = DashboardBuiltEvent::make($widgets);
        $this->eventDispatcher->dispatch($event);

        return $this->render('@BeaconAdmin/dashboard.html.twig', [
            'widgets' => $event->getWidgets(),
        ]);
    }
}
