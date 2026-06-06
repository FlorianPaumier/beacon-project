<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Exception;

/**
 * Base logic exception for BeaconAdmin bundle.
 *
 * Use for programming errors that should be fixed in code:
 * misconfiguration, missing services, invalid wiring.
 */
class BeaconAdminLogicException extends \LogicException implements BeaconAdminException
{
}
