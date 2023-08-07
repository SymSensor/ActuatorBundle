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

namespace SymSensor\ActuatorBundle\Service\Health\Indicator;

use SymSensor\ActuatorBundle\Service\Health\HealthInterface;

interface HealthIndicator
{
    public function name(): string;

    public function health(): HealthInterface;
}
