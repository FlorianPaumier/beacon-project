<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Notification;

final readonly class Notification
{
    public const DEFAULT_DURATION = 5000;

    public function __construct(
        public NotificationType $type,
        public string $message,
        public ?string $actionLabel = null,
        public ?string $actionUrl = null,
        public int $duration = self::DEFAULT_DURATION,
    ) {
    }

    public static function success(string $message): self
    {
        return new self(NotificationType::Success, $message);
    }

    public static function error(string $message): self
    {
        return new self(NotificationType::Error, $message);
    }

    public static function warning(string $message): self
    {
        return new self(NotificationType::Warning, $message);
    }

    public static function info(string $message): self
    {
        return new self(NotificationType::Info, $message);
    }

    public function action(string $label, string $url): self
    {
        return new self(
            $this->type,
            $this->message,
            $label,
            $url,
            $this->duration,
        );
    }

    public function duration(int $ms): self
    {
        return new self(
            $this->type,
            $this->message,
            $this->actionLabel,
            $this->actionUrl,
            $ms,
        );
    }

    public function sticky(): self
    {
        return $this->duration(0);
    }

    public function isSticky(): bool
    {
        return $this->duration === 0;
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'type' => $this->type->value,
            'message' => $this->message,
            'action_label' => $this->actionLabel,
            'action_url' => $this->actionUrl,
            'duration' => $this->duration,
            'sticky' => $this->isSticky(),
        ];
    }
}
