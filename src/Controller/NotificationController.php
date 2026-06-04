<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Controller;

use Devgeek\BeaconAdmin\Notification\NotificationManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
final class NotificationController extends AbstractController
{
    public function __construct(
        private readonly NotificationManager $manager,
        private readonly string $adminRole = 'ROLE_ADMIN',
    ) {
    }

    public static function make(NotificationManager $manager, string $adminRole = 'ROLE_ADMIN'): self
    {
        return new self($manager, $adminRole);
    }

    #[Route('/%beacon_admin.route_prefix%/notifications/unread-count', name: 'beacon_admin_notifications_unread_count', methods: ['GET'])]
    public function unreadCount(): JsonResponse
    {
        $this->denyAccessUnlessGranted($this->adminRole);

        $items = $this->manager->peek();

        $itemsArray = array_map(static fn ($n) => $n->toArray(), $items);

        return new JsonResponse([
            'count' => count($itemsArray),
            'items' => $itemsArray,
        ]);
    }

    #[Route('/%beacon_admin.route_prefix%/notifications', name: 'beacon_admin_notifications_index', methods: ['GET'])]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted($this->adminRole);

        $notifications = $this->manager->all();

        return $this->render('@BeaconAdmin/notification/inbox.html.twig', [
            'notifications' => $notifications,
        ]);
    }
}
