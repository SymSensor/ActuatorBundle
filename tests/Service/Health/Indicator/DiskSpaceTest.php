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

namespace SymSensor\ActuatorBundle\Tests\Service\Health\Indicator;

use PHPUnit\Framework\TestCase;
use SymSensor\ActuatorBundle\Service\Health\Health;
use SymSensor\ActuatorBundle\Service\Health\Indicator\DiskSpace;

class DiskSpaceTest extends TestCase
{
    /**
     * @test
     */
    public function indicatorName(): void
    {
        $diskSpaceHealthIndicator = new DiskSpace(
            \sys_get_temp_dir(),
            10000
        );

        self::assertEquals('diskSpace', $diskSpaceHealthIndicator->name());
    }

    /**
     * @test
     */
    public function notHealthyIfDiskFreeSpaceReturnedFalse(): void
    {
        // given
        $diskSpaceHealthIndicator = new DiskSpace(
            '/not-existing',
            10000
        );

        // when
        $health = $diskSpaceHealthIndicator->health();

        // then
        self::assertEquals(Health::UNKNOWN, $health->getStatus());
    }

    /**
     * @test
     */
    public function notHealthyIfDiskFreeSpaceIsBelowThreshold(): void
    {
        // given
        $diskSpaceHealthIndicator = new DiskSpace(
            \sys_get_temp_dir(),
            \PHP_INT_MAX
        );

        // when
        $health = $diskSpaceHealthIndicator->health();

        // then
        self::assertInstanceOf(Health::class, $health);
        self::assertEquals(Health::DOWN, $health->getStatus());

        self::assertArrayHasKey('disk_free_space', $health->getDetails());
        self::assertEquals(\disk_free_space(\sys_get_temp_dir()), $health->getDetails()['disk_free_space']);

        self::assertArrayHasKey('threshold', $health->getDetails());
        self::assertEquals(\PHP_INT_MAX, $health->getDetails()['threshold']);
    }

    /**
     * @test
     */
    public function healthyIfDiskFreeSpaceIsBelowThreshold(): void
    {
        // given
        $diskSpaceHealthIndicator = new DiskSpace(
            \sys_get_temp_dir(),
            0
        );

        // when
        $health = $diskSpaceHealthIndicator->health();

        // then
        self::assertInstanceOf(Health::class, $health);
        self::assertEquals(Health::UP, $health->getStatus());

        self::assertArrayHasKey('disk_free_space', $health->getDetails());
        self::assertEquals(\disk_free_space(\sys_get_temp_dir()), $health->getDetails()['disk_free_space']);

        self::assertArrayHasKey('threshold', $health->getDetails());
        self::assertEquals(0, $health->getDetails()['threshold']);
    }
}
