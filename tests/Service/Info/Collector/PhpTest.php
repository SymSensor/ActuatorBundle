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

use PHPUnit\Framework\TestCase;
use SymSensor\ActuatorBundle\Service\Info\Collector\Php;

class PhpTest extends TestCase
{
    private Php $php;

    protected function setUp(): void
    {
        $this->php = new Php();
    }

    /**
     * @test
     */
    public function nameWillBePhp(): void
    {
        self::assertEquals('php', $this->php->collect()->name());
    }

    /**
     * @test
     */
    public function phpInformations(): void
    {
        // when
        $info = $this->php->collect();

        // then
        self::assertFalse($info->isEmpty());
    }
}
