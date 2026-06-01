<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Security;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Votes on #[BeaconAccess] attributes on controller classes and methods.
 *
 * Registered automatically via autoconfigure (implements VoterInterface).
 * Consuming apps implement their own VoterInterface for custom permissions.
 */
final class BeaconAccessVoter extends Voter
{
    public function __construct(
        private readonly string $adminRole = 'ROLE_ADMIN',
    ) {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute instanceof BeaconAccess;
    }

    /** @param BeaconAccess $attribute */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        \assert($attribute instanceof BeaconAccess);

        // If the attribute specifies a role, check it directly
        if (null !== $attribute->role) {
            return $this->hasRole($token, $attribute->role);
        }

        // Custom permission string — fail-closed: the app MUST provide a voter
        // that explicitly grants. Returning false ensures a missing voter
        // results in denied access rather than silently granting it.
        if (null !== $attribute->permission) {
            return false;
        }

        // Neither role nor permission set — fall back to configured admin role
        return $this->hasRole($token, $this->adminRole);
    }

    private function hasRole(TokenInterface $token, string $role): bool
    {
        return \in_array($role, $token->getRoleNames(), true);
    }
}
