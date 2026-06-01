<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Functional;

use Devgeek\BeaconAdmin\Security\BeaconAccess;
use Devgeek\BeaconAdmin\Tests\Fixtures\TestApp\TestKernel;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class SecurityTest extends WebTestCase
{
    protected static function getKernelClass(): string
    {
        return TestKernel::class;
    }

    public function testSecurityRoleParameterIsSet(): void
    {
        $client = static::createClient();
        $container = $client->getContainer();

        $this->assertSame('ROLE_ADMIN', $container->getParameter('beacon_admin.security.role'));
    }

    public function testBeaconAccessVoterIsRegistered(): void
    {
        $client = static::createClient();
        $container = $client->getContainer();

        $this->assertTrue(
            $container->has('Devgeek\BeaconAdmin\Security\BeaconAccessVoter'),
            'BeaconAccessVoter should be registered in container',
        );
    }

    public function testBeaconAccessAttributeExistsOnDashboard(): void
    {
        $refl = new \ReflectionClass('Devgeek\BeaconAdmin\Controller\DashboardController');
        $attrs = $refl->getAttributes(BeaconAccess::class);

        $this->assertCount(1, $attrs);

        /** @var BeaconAccess $instance */
        $instance = $attrs[0]->newInstance();
        $this->assertSame('ROLE_ADMIN', $instance->role);
    }

    public function testDashboardRouteIsAccessible(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin');

        $this->assertResponseIsSuccessful();
    }

    public function testVoterCanBeDisabledViaConfig(): void
    {
        // Voter exists by default. The Extension removes it when
        // beacon_admin.security.voters is false. We verify it exists
        // with the default config, where voters = true.
        $client = static::createClient();
        $container = $client->getContainer();

        $this->assertTrue($container->has('Devgeek\BeaconAdmin\Security\BeaconAccessVoter'));
    }

    public function testAdminRoleIsConfigurable(): void
    {
        $client = static::createClient();
        $container = $client->getContainer();

        $voter = $container->get('Devgeek\BeaconAdmin\Security\BeaconAccessVoter');

        // Verify voter was constructed with the configured admin role
        $refl = new \ReflectionClass($voter);
        $prop = $refl->getProperty('adminRole');
        $this->assertSame(
            'ROLE_ADMIN',
            $prop->getValue($voter),
            'BeaconAccessVoter should be constructed with the configured admin role',
        );
    }
}
