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

namespace SymSensor\ActuatorBundle\Tests\Service\Info;

use PHPUnit\Framework\TestCase;
use SymSensor\ActuatorBundle\Service\Info\Info;
use SymSensor\ActuatorBundle\Service\Info\InfoStack;

class InfoStackTest extends TestCase
{
    /**
     * @test
     */
    public function stackDoesNotReturnEmptyInfo(): void
    {
        // given
        $info = new Info('someName', []);
        $infoStack = new InfoStack([$info]);

        // when
        $result = $infoStack->jsonSerialize();

        // then
        self::assertEmpty($result);
    }

    /**
     * @test
     */
    public function stackReturnInfoWithName(): void
    {
        // given
        $info = new Info('someName', ['someParam' => 'someValue']);
        $infoStack = new InfoStack([$info]);

        // when
        $result = $infoStack->jsonSerialize();

        // then
        self::assertNotEmpty($result);
        self::assertArrayHasKey('someName', $result);
        self::assertIsArray($result['someName']);
        self::assertEquals(['someParam' => 'someValue'], $result['someName']);
    }
}
