<?php

declare(strict_types=1);

/*
 * This file is part of the symsensor/actuator-bundle package.
 *
 * (c) Kevin Studer <kreemer@me.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymSensor\ActuatorBundle\Tests\Controller;

use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\KernelInterface;
use SymSensor\ActuatorBundle\Tests\Kernel;

abstract class ControllerTestCase extends TestCase
{
    protected KernelBrowser $client;
    protected KernelInterface $kernel;

    protected function setUp(): void
    {
        $this->kernel = new Kernel('test', false);
        $this->client = new KernelBrowser($this->kernel);
        $this->kernel->boot();
    }

    protected function tearDown(): void
    {
        $this->deleteCache();
    }

    /**
     * @param array<mixed> $config
     */
    protected function rebootKernelWithConfig(array $config = []): void
    {
        $this->deleteCache();
        $this->kernel->shutdown();

        $this->kernel = new Kernel('test', false, $config);
        $this->client = new KernelBrowser($this->kernel);
        $this->kernel->boot();
    }

    protected function deleteCache(): void
    {
        $cacheDir = $this->kernel->getCacheDir();

        $filesystem = new Filesystem();

        if ($filesystem->exists($cacheDir)) {
            $filesystem->remove($cacheDir);
        }
    }
}
