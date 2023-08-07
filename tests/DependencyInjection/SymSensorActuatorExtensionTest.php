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

namespace SymSensor\ActuatorBundle\Tests\DependencyInjection;

use Composer\Autoload\ClassLoader;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use SymSensor\ActuatorBundle\DependencyInjection\SymSensorActuatorExtension;
use SymSensor\ActuatorBundle\Service\Health\Indicator\DiskSpace;
use SymSensor\ActuatorBundle\Service\Info\Collector\Git;
use SymSensor\ActuatorBundle\Service\Info\Collector\Php;
use SymSensor\ActuatorBundle\Service\Info\Collector\Symfony;

class SymSensorActuatorExtensionTest extends TestCase
{
    private SymSensorActuatorExtension $extension;
    private ContainerBuilder $containerBuilder;
    private ?ClassLoader $classLoader = null;
    private vfsStreamDirectory $root;

    protected function setUp(): void
    {
        parent::setUp();

        $this->extension = new SymSensorActuatorExtension();
        $this->containerBuilder = new ContainerBuilder();

        $this->root = vfsStream::setup(\uniqid());
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        if (null !== $this->classLoader) {
            $this->classLoader->unregister();
        }
    }

    /**
     * @test
     */
    public function defaultConfigHasHealthFromEnvVariable(): void
    {
        // when
        $this->extension->load([], $this->containerBuilder);

        // then
        self::assertTrue($this->containerBuilder->hasParameter('sym_sensor_actuator.health.enabled'));
        self::assertTrue($this->containerBuilder->getParameter('sym_sensor_actuator.health.enabled'));
    }

    /**
     * @test
     */
    public function healthEnabledCanBeSetWithConfig(): void
    {
        // when
        $this->extension->load(['sym_sensor_actuator' => ['health' => ['enabled' => false]]], $this->containerBuilder);

        // then
        self::assertTrue($this->containerBuilder->hasParameter('sym_sensor_actuator.health.enabled'));
        self::assertFalse($this->containerBuilder->getParameter('sym_sensor_actuator.health.enabled'));
    }

    /**
     * @test
     */
    public function healthBuiltinDiskSpaceIsEnabledByDefault(): void
    {
        // when
        $this->extension->load([], $this->containerBuilder);

        // then
        self::assertTrue($this->containerBuilder->hasDefinition(DiskSpace::class));
    }

    /**
     * @test
     */
    public function healthBuiltinDiskSpaceHasDefaultThreshold(): void
    {
        // when
        $this->extension->load([], $this->containerBuilder);

        // then
        self::assertTrue($this->containerBuilder->hasDefinition(DiskSpace::class));
        self::assertCount(2, $this->containerBuilder->getDefinition(DiskSpace::class)->getArguments());
        self::assertEquals(50 * 1024 * 1024, $this->containerBuilder->getDefinition(DiskSpace::class)->getArguments()[1]);
    }

    /**
     * @test
     */
    public function healthBuiltinDiskSpaceHasDefaultDirectory(): void
    {
        // when
        $this->extension->load([], $this->containerBuilder);

        // then
        self::assertTrue($this->containerBuilder->hasDefinition(DiskSpace::class));
        self::assertCount(2, $this->containerBuilder->getDefinition(DiskSpace::class)->getArguments());
        self::assertEquals('%kernel.project_dir%', $this->containerBuilder->getDefinition(DiskSpace::class)->getArguments()[0]);
    }

    /**
     * @test
     */
    public function healthBuiltinDiskSpaceCanBeDisabled(): void
    {
        // given
        $config = ['sym_sensor_actuator' => ['health' => ['builtin' => ['disk_space' => ['enabled' => false]]]]];

        // when
        $this->extension->load($config, $this->containerBuilder);

        // then
        self::assertFalse($this->containerBuilder->hasDefinition(DiskSpace::class));
    }

    /**
     * @test
     */
    public function healthBuiltinDiskSpaceThresholdCanBeOverwritten(): void
    {
        // given
        $config = ['sym_sensor_actuator' => ['health' => ['builtin' => ['disk_space' => ['threshold' => 1024]]]]];

        // when
        $this->extension->load($config, $this->containerBuilder);

        // then
        self::assertTrue($this->containerBuilder->hasDefinition(DiskSpace::class));
        self::assertCount(2, $this->containerBuilder->getDefinition(DiskSpace::class)->getArguments());
        self::assertEquals(1024, $this->containerBuilder->getDefinition(DiskSpace::class)->getArguments()[1]);
    }

    /**
     * @test
     */
    public function healthBuiltinDiskSpaceDirectoryCanBeOverwritten(): void
    {
        // given
        $config = ['sym_sensor_actuator' => ['health' => ['builtin' => ['disk_space' => ['path' => 'someValue']]]]];

        // when
        $this->extension->load($config, $this->containerBuilder);

        // then
        self::assertTrue($this->containerBuilder->hasDefinition(DiskSpace::class));
        self::assertCount(2, $this->containerBuilder->getDefinition(DiskSpace::class)->getArguments());
        self::assertEquals('someValue', $this->containerBuilder->getDefinition(DiskSpace::class)->getArguments()[0]);
    }

    /**
     * @test
     */
    public function infoIsEnabledByDefault(): void
    {
        // given
        $config = [];

        // when
        $this->extension->load($config, $this->containerBuilder);

        // then
        self::assertTrue($this->containerBuilder->hasParameter('sym_sensor_actuator.info.enabled'));
        self::assertTrue($this->containerBuilder->getParameter('sym_sensor_actuator.info.enabled'));
    }

    /**
     * @test
     */
    public function infoCanBeDisabled(): void
    {
        // given
        $config = ['sym_sensor_actuator' => ['info' => ['enabled' => false]]];

        // when
        $this->extension->load($config, $this->containerBuilder);

        // then
        self::assertTrue($this->containerBuilder->hasParameter('sym_sensor_actuator.info.enabled'));
        self::assertFalse($this->containerBuilder->getParameter('sym_sensor_actuator.info.enabled'));
    }

    /**
     * @test
     */
    public function infoBuiltinDefaultList(): void
    {
        // given
        $config = [];

        // when
        $this->extension->load($config, $this->containerBuilder);

        // then
        self::assertTrue($this->containerBuilder->hasDefinition(Php::class));
        self::assertTrue($this->containerBuilder->hasDefinition(Symfony::class));
        self::assertTrue($this->containerBuilder->hasDefinition(Git::class));
    }

    /**
     * @test
     */
    public function infoBuiltinListCanBeEmpty(): void
    {
        // given
        $config = ['actuator' => ['info' => ['builtin' => []]]];

        // when
        $this->extension->load($config, $this->containerBuilder);

        // then
        self::assertTrue($this->containerBuilder->hasDefinition(Php::class));
        self::assertTrue($this->containerBuilder->hasDefinition(Symfony::class));
        self::assertTrue($this->containerBuilder->hasDefinition(Git::class));
    }

    /**
     * @test
     */
    public function infoBuiltinListCanBeDefined(): void
    {
        // given
        $config = ['sym_sensor_actuator' => ['info' => ['builtin' => ['php' => ['enabled' => true]]]]];

        // when
        $this->extension->load($config, $this->containerBuilder);

        // then
        self::assertTrue($this->containerBuilder->hasDefinition(Php::class));
    }

    /**
     * @test
     */
    public function infoBuiltinListCanBeDefinedWithMultipleEntries(): void
    {
        // given
        $config = ['sym_sensor_actuator' => ['info' => ['builtin' => ['git' => ['enabled' => false]]]]];

        // when
        $this->extension->load($config, $this->containerBuilder);

        // then
        self::assertFalse($this->containerBuilder->hasDefinition(Git::class));
    }

    protected function registerClassLoader(): void
    {
        if (null !== $this->classLoader) {
            throw new \RuntimeException('Classloader already registered');
        }

        $this->classLoader = new ClassLoader($this->root->url());
        $this->classLoader->register(true);
    }
}
