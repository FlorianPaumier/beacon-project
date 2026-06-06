<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Controller;

use Symfony\Component\HttpKernel\Attribute\AsController;

/**
 * Default dashboard controller — auto-used when the app doesn't provide its own.
 *
 * To customize, create your own DashboardController extending
 * AbstractDashboardController and override the configure*() methods.
 */
#[AsController]
final class DashboardController extends AbstractDashboardController
{
}
