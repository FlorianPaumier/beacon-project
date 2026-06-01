<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Security;

/**
 * Access control attribute for admin controllers and CRUD operations.
 *
 * Usage:
 *   #[BeaconAccess::make()->role('ROLE_SUPER_ADMIN')]
 *   #[BeaconAccess::make()->permission('entity.edit')]
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class BeaconAccess
{
    protected ?string $role = null;
    protected ?string $permission = null;

    public function __construct(?string $role = null, ?string $permission = null)
    {
        if (null !== $role) {
            $this->role = $role;
        }
        if (null !== $permission) {
            $this->permission = $permission;
        }
    }

    public static function make(): static
    {
        return new static();
    }

    public function role(?string $role): static
    {
        $this->role = $role;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function permission(?string $permission): static
    {
        $this->permission = $permission;

        return $this;
    }

    public function getPermission(): ?string
    {
        return $this->permission;
    }
}
