<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Security;

/**
 * Attribute to control access on admin controllers and CRUD operations.
 *
 * Place on a controller class (applies to all methods) or individual method.
 * When omitted, the default role from beacon_admin.security.role is checked.
 *
 * Usage:
 *   #[BeaconAccess(role: 'ROLE_SUPER_ADMIN')]
 *   #[BeaconAccess(permission: 'entity.edit')]
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
final readonly class BeaconAccess
{
    /** The Symfony security role required (e.g. ROLE_ADMIN). */
    public ?string $role;

    /** A custom permission string checked by your own voter. */
    public ?string $permission;

    public function __construct(?string $role = null, ?string $permission = null)
    {
        if (null === $role && null === $permission) {
            throw new \InvalidArgumentException('BeaconAccess requires at least one of: role, permission.');
        }

        $this->role = $role;
        $this->permission = $permission;
    }
}
