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

namespace SymSensor\ActuatorBundle\Tests\Service\Info\Collector;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use SymSensor\ActuatorBundle\Service\Info\Collector\Symfony;

class SymfonyTest extends TestCase
{
    /**
     * @var KernelInterface&MockObject
     */
    private KernelInterface $kernel;

    private Symfony $symfony;

    protected function setUp(): void
    {
        $this->kernel = self::createMock(KernelInterface::class);

        $this->symfony = new Symfony($this->kernel);
    }

    /**
     * @test
     */
    public function nameWillBeSymfony(): void
    {
        $this->kernel->method('getBundles')
            ->willReturn([]);

        self::assertEquals('symfony', $this->symfony->collect()->name());
    }

    /**
     * @test
     */
    public function symfonyInformations(): void
    {
        // given
        $this->kernel->method('getBundles')
            ->willReturn([]);

        // when
        $info = $this->symfony->collect();

        // then
        self::assertFalse($info->isEmpty());
    }

    /**
     * @test
     */
    public function environmentIsReadFromKernel(): void
    {
        // given
        $this->kernel->method('getEnvironment')
            ->willReturn('someValue');

        $this->kernel->method('getBundles')
            ->willReturn([]);

        // when
        $info = $this->symfony->collect();

        // then
        self::assertArrayHasKey('environment', $info->jsonSerialize());
        self::assertEquals('someValue', $info->jsonSerialize()['environment']);
    }

    /**
     * @test
     */
    public function bundlesAreReadFromKernel(): void
    {
        // given
        $bundle = self::createMock(BundleInterface::class);
        $class = $bundle::class;
        $this->kernel->method('getBundles')
            ->willReturn([$bundle]);

        // when
        $info = $this->symfony->collect();

        // then
        self::assertArrayHasKey('bundles', $info->jsonSerialize());
        self::assertIsArray($info->jsonSerialize()['bundles']);
        self::assertCount(1, $info->jsonSerialize()['bundles']);
        self::assertEquals($class, $info->jsonSerialize()['bundles'][0]);
    }
}
