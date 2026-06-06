<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Exception;

/**
 * Marker interface for all exceptions thrown by BeaconAdmin.
 *
 * Catch this to handle any bundle-originated error:
 *   try { ... } catch (BeaconAdminException $e) { ... }
 */
interface BeaconAdminException extends \Throwable
{
}
