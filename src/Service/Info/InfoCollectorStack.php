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

namespace SymSensor\ActuatorBundle\Service\Info;

use SymSensor\ActuatorBundle\Service\Info\Collector\Collector;

class InfoCollectorStack
{
    /**
     * @var Collector[]
     */
    private iterable $collectors;

    /**
     * @param Collector[] $collectors
     */
    public function __construct(iterable $collectors)
    {
        $this->collectors = $collectors;
    }

    public function collect(): InfoStack
    {
        $infos = [];
        foreach ($this->collectors as $collector) {
            $infos[] = $collector->collect();
        }

        return new InfoStack($infos);
    }
}
