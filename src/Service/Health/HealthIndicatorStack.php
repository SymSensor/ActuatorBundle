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

namespace SymSensor\ActuatorBundle\Service\Health;

use SymSensor\ActuatorBundle\Service\Health\Indicator\HealthIndicator;

class HealthIndicatorStack
{
    /**
     * @var iterable<HealthIndicator>
     */
    private iterable $indicators;

    /**
     * @param iterable<HealthIndicator> $indicators
     */
    public function __construct(iterable $indicators)
    {
        $this->indicators = $indicators;
    }

    public function check(): HealthStack
    {
        $details = [];

        foreach ($this->indicators as $indicator) {
            $details[$indicator->name()] = $indicator->health();
        }

        return new HealthStack($details);
    }
}
