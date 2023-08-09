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

namespace SymSensor\ActuatorBundle\Tests\Service\Health;

use PHPUnit\Framework\TestCase;
use SymSensor\ActuatorBundle\Service\Health\Health;
use SymSensor\ActuatorBundle\Service\Health\HealthIndicatorStack;
use SymSensor\ActuatorBundle\Service\Health\HealthState;
use SymSensor\ActuatorBundle\Service\Health\Indicator\HealthIndicator;

class HealthIndicatorStackTest extends TestCase
{
    /**
     * @test
     */
    public function overallStateIsUpIfAllIndicatorsAreUp(): void
    {
        // given
        $indicator1 = self::createMock(HealthIndicator::class);
        $indicator1->method('health')
            ->willReturn(Health::up());

        $indicator2 = self::createMock(HealthIndicator::class);
        $indicator2->method('health')
            ->willReturn(Health::up());

        $stack = new HealthIndicatorStack([$indicator1, $indicator2]);
        // when
        $response = $stack->check()->jsonSerialize();

        // then
        self::assertArrayHasKey('status', $response);
        self::assertEquals(HealthState::UP, $response['status']);
    }

    /**
     * @test
     */
    public function overallStateIsDownIfOneIndicatorIsDown(): void
    {
        // given
        $indicator1 = self::createMock(HealthIndicator::class);
        $indicator1->method('health')
            ->willReturn(Health::up());

        $indicator2 = self::createMock(HealthIndicator::class);
        $indicator2->method('health')
            ->willReturn(Health::down());

        $stack = new HealthIndicatorStack([$indicator1, $indicator2]);
        // when
        $response = $stack->check()->jsonSerialize();

        // then
        self::assertArrayHasKey('status', $response);
        self::assertEquals(HealthState::DOWN, $response['status']);
    }

    /**
     * @test
     */
    public function overallStateIsUnknownIfOneIndicatorIsUnknown(): void
    {
        // given
        $indicator1 = self::createMock(HealthIndicator::class);
        $indicator1->method('health')
            ->willReturn(Health::up());

        $indicator2 = self::createMock(HealthIndicator::class);
        $indicator2->method('health')
            ->willReturn(Health::unknown());

        $stack = new HealthIndicatorStack([$indicator1, $indicator2]);

        // when
        $response = $stack->check()->jsonSerialize();

        // then
        self::assertArrayHasKey('status', $response);
        self::assertEquals(HealthState::UNKNOWN, $response['status']);
    }
}
