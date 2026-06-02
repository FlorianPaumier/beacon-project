<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Security;

use Devgeek\BeaconAdmin\Security\BeaconAccess;
use Devgeek\BeaconAdmin\Security\BeaconAccessVoter;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

final class BeaconAccessVoterTest extends TestCase
{
    #[Test]
    public function itCreatesViaMake(): void
    {
        $voter = BeaconAccessVoter::make();

        $this->assertInstanceOf(BeaconAccessVoter::class, $voter);
    }

    #[Test]
    public function itHasDefaultAdminRole(): void
    {
        $voter = new BeaconAccessVoter();

        $this->assertSame('ROLE_ADMIN', $voter->getAdminRole());
    }

    #[Test]
    public function itSetsCustomAdminRole(): void
    {
        $voter = new BeaconAccessVoter('ROLE_SUPER_ADMIN');

        $this->assertSame('ROLE_SUPER_ADMIN', $voter->getAdminRole());
    }

    #[Test]
    public function itSetsAdminRoleFluently(): void
    {
        $voter = BeaconAccessVoter::make()->adminRole('ROLE_MANAGER');

        $this->assertSame('ROLE_MANAGER', $voter->getAdminRole());
    }

    #[Test]
    public function itGrantsAccessForAdminRole(): void
    {
        $voter = new BeaconAccessVoter();
        $token = $this->createMock(TokenInterface::class);
        $token->method('getRoleNames')->willReturn(['ROLE_ADMIN']);

        $result = $voter->vote($token, null, [new BeaconAccess()]);

        $this->assertSame(VoterInterface::ACCESS_GRANTED, $result);
    }

    #[Test]
    public function itAbstainsForNonAdminRole(): void
    {
        $voter = new BeaconAccessVoter();
        $token = $this->createMock(TokenInterface::class);
        $token->method('getRoleNames')->willReturn(['ROLE_USER']);

        $result = $voter->vote($token, null, [new BeaconAccess()]);

        $this->assertSame(VoterInterface::ACCESS_ABSTAIN, $result);
    }

    #[Test]
    public function itGrantsAccessForCustomRole(): void
    {
        $voter = new BeaconAccessVoter('ROLE_MANAGER');
        $token = $this->createMock(TokenInterface::class);
        $token->method('getRoleNames')->willReturn(['ROLE_MANAGER']);

        $result = $voter->vote($token, null, [new BeaconAccess()]);

        $this->assertSame(VoterInterface::ACCESS_GRANTED, $result);
    }

    #[Test]
    public function itGrantsAccessForSpecificRoleAttribute(): void
    {
        $voter = new BeaconAccessVoter();
        $token = $this->createMock(TokenInterface::class);
        $token->method('getRoleNames')->willReturn(['ROLE_EDITOR']);

        $result = $voter->vote($token, null, [new BeaconAccess(role: 'ROLE_EDITOR')]);

        $this->assertSame(VoterInterface::ACCESS_GRANTED, $result);
    }

    #[Test]
    public function itAbstainsForNonBeaconAccessAttributes(): void
    {
        $voter = new BeaconAccessVoter();

        $token = $this->createMock(TokenInterface::class);

        $result = $voter->vote($token, null, ['not_beacon_access']);

        $this->assertSame(VoterInterface::ACCESS_ABSTAIN, $result);
    }

    #[Test]
    public function itReturnsAbstainForPermissionAttribute(): void
    {
        $voter = new BeaconAccessVoter();
        $token = $this->createMock(TokenInterface::class);
        $token->method('getRoleNames')->willReturn(['ROLE_ADMIN']);

        $result = $voter->vote($token, null, [new BeaconAccess(permission: 'custom_permission')]);

        $this->assertSame(VoterInterface::ACCESS_ABSTAIN, $result);
    }

    #[Test]
    public function itSupportsReturnsFalse(): void
    {
        $voter = new BeaconAccessVoter();
        $token = $this->createMock(TokenInterface::class);

        $result = $voter->vote($token, null, []);

        $this->assertSame(VoterInterface::ACCESS_ABSTAIN, $result);
    }

    #[Test]
    public function itGrantsWhenMultipleAttributesContainMatchingRole(): void
    {
        $voter = new BeaconAccessVoter();
        $token = $this->createMock(TokenInterface::class);
        $token->method('getRoleNames')->willReturn(['ROLE_EDITOR']);

        $result = $voter->vote($token, null, [
            new BeaconAccess(role: 'ROLE_ADMIN'),
            new BeaconAccess(role: 'ROLE_EDITOR'),
        ]);

        $this->assertSame(VoterInterface::ACCESS_GRANTED, $result);
    }
}
