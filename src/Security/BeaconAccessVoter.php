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
class BeaconAccessVoter extends Voter
{
    protected string $adminRole;

    public static function make(): static
    {
        return new static();
    }

    public function __construct(string $adminRole = 'ROLE_ADMIN')
    {
        $this->adminRole = $adminRole;
    }

    public function adminRole(string $adminRole): static
    {
        $this->adminRole = $adminRole;

        return $this;
    }

    public function getAdminRole(): string
    {
        return $this->adminRole;
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute instanceof BeaconAccess;
    }

    /** @param BeaconAccess $attribute */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        \assert($attribute instanceof BeaconAccess);

        if (null !== $attribute->getRole()) {
            return $this->hasRole($token, $attribute->getRole());
        }

        // Custom permission — fail-closed: app MUST provide a granting voter
        if (null !== $attribute->getPermission()) {
            return false;
        }

        return $this->hasRole($token, $this->adminRole);
    }

    private function hasRole(TokenInterface $token, string $role): bool
    {
        return \in_array($role, $token->getRoleNames(), true);
    }
}
