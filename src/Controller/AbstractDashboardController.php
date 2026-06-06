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

/**
 * Extensible dashboard controller — EasyAdmin-style.
 *
 * Override configure*() methods in your app's controller to customize
 * the admin dashboard without touching YAML/PHP config files.
 */
#[AsController]
#[BeaconAccess(role: 'ROLE_ADMIN')]
abstract class AbstractDashboardController extends AbstractController
{
    protected WidgetRegistry $widgets;
    protected EventDispatcherInterface $eventDispatcher;

    public function __construct(
        WidgetRegistry $widgets,
        EventDispatcherInterface $eventDispatcher,
    ) {
        $this->widgets = $widgets;
        $this->eventDispatcher = $eventDispatcher;
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

    /**
     * Override to define the admin menu.
     *
     * @return array<int, array{label: string, route?: string, icon?: string, role?: string, children?: array<int, mixed>}>
     */
    public function configureMenuItems(): array
    {
        return [];
    }

    /**
     * Override to set the admin page title.
     */
    public function configureTitle(): ?string
    {
        return null;
    }

    /**
     * Override to define brand settings.
     *
     * @return array{name?: string, logo_path?: ?string, favicon_path?: ?string, primary_color?: string, accent_color?: string, support_email?: ?string}
     */
    public function configureBrand(): array
    {
        return [];
    }

    /**
     * Override to define custom themes.
     *
     * @return array<string, string>
     */
    public function configureThemes(): array
    {
        return [];
    }

    /**
     * Override to set the default theme.
     */
    public function configureDefaultTheme(): ?string
    {
        return null;
    }
}
