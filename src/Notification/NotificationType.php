<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Notification;

enum NotificationType: string
{
    case Success = 'success';
    case Error = 'error';
    case Warning = 'warning';
    case Info = 'info';

    public function isSticky(): bool
    {
        return $this === self::Error || $this === self::Warning;
    }
}
