<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Security;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

/**
 * @extends Voter<string, mixed>
 */
class BeaconAccessVoter extends Voter
{
    protected string $adminRole;

    public static function make(): self
    {
        return new self();
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

    /**
     * Override vote() directly because the parent supports() has signature
     * `string $attribute` — BeaconAccess objects cause a TypeError that gets
     * silently caught and skipped, resulting in ACCESS_ABSTAIN.
     */
    public function vote(TokenInterface $token, mixed $subject, array $attributes, ?Vote $vote = null): int
    {
        foreach ($attributes as $attribute) {
            if (!$attribute instanceof BeaconAccess) {
                continue;
            }

            if (null !== $vote) {
                $vote->result = VoterInterface::ACCESS_DENIED;
            }

            if ($this->voteOn($token, $attribute)) {
                if (null !== $vote) {
                    $vote->result = VoterInterface::ACCESS_GRANTED;
                }

                return VoterInterface::ACCESS_GRANTED;
            }
        }

        return VoterInterface::ACCESS_ABSTAIN;
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return false;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        return false;
    }

    private function voteOn(TokenInterface $token, BeaconAccess $attribute): bool
    {
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
