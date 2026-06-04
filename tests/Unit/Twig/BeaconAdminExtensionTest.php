<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Unit\Twig;

use Devgeek\BeaconAdmin\Twig\BeaconAdminExtension;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class BeaconAdminExtensionTest extends TestCase
{
    #[Test]
    public function itRegistersFunctions(): void
    {
        $extension = new BeaconAdminExtension();

        $functions = $extension->getFunctions();

        $this->assertCount(6, $functions);
    }

    #[Test]
    public function itRegistersBeaconAdminConfigFunction(): void
    {
        $extension = new BeaconAdminExtension();

        $functions = $extension->getFunctions();
        $names = array_map(fn ($f) => $f->getName(), $functions);

        $this->assertContains('beacon_admin_config', $names);
    }

    #[Test]
    public function itRegistersBeaconAdminThemeFunction(): void
    {
        $extension = new BeaconAdminExtension();

        $functions = $extension->getFunctions();
        $names = array_map(fn ($f) => $f->getName(), $functions);

        $this->assertContains('beacon_admin_theme', $names);
    }

    #[Test]
    public function itRegistersBeaconAdminMenuFunction(): void
    {
        $extension = new BeaconAdminExtension();

        $functions = $extension->getFunctions();
        $names = array_map(fn ($f) => $f->getName(), $functions);

        $this->assertContains('beacon_admin_menu', $names);
    }

    #[Test]
    public function itRegistersBeaconAdminWidgetsFunction(): void
    {
        $extension = new BeaconAdminExtension();

        $functions = $extension->getFunctions();
        $names = array_map(fn ($f) => $f->getName(), $functions);

        $this->assertContains('beacon_admin_widgets', $names);
    }

    #[Test]
    public function itRegistersBeaconAdminThemesFunction(): void
    {
        $extension = new BeaconAdminExtension();

        $functions = $extension->getFunctions();
        $names = array_map(fn ($f) => $f->getName(), $functions);

        $this->assertContains('beacon_admin_themes', $names);
    }

    #[Test]
    public function itCreatesViaMake(): void
    {
        $extension = BeaconAdminExtension::make();

        $this->assertNotEmpty($extension->getFunctions());
    }
}
