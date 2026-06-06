<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Exception;

/**
 * Base runtime exception for BeaconAdmin bundle.
 *
 * Use for errors that occur during normal operation:
 * entity not found, invalid state transitions, etc.
 */
class BeaconAdminRuntimeException extends \RuntimeException implements BeaconAdminException
{
}
