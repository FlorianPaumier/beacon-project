<?php

declare(strict_types=1);

namespace Devgeek\BeaconAdmin\Tests\Fixtures\TestApp;

use Devgeek\BeaconAdmin\BeaconAdminBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

final class TestKernel extends Kernel
{
    /** @return iterable<\Symfony\Component\HttpKernel\Bundle\BundleInterface> */
    public function registerBundles(): iterable
    {
        return [
            new FrameworkBundle(),
            new SecurityBundle(),
            new TwigBundle(),
            new BeaconAdminBundle(),
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/config/config.yaml');
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir() . '/beacon-admin/cache/' . $this->environment;
    }

    public function getLogDir(): string
    {
        return sys_get_temp_dir() . '/beacon-admin/logs';
    }
}
