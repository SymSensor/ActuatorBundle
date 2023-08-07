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

use SymSensor\ActuatorBundle\Service\Health\Health;
use SymSensor\ActuatorBundle\Service\Health\HealthInterface;

class DiskSpace implements HealthIndicator
{
    private string $path;
    private int $threshold;

    public function __construct(string $path, int $threshold)
    {
        $this->path = $path;
        $this->threshold = $threshold;
    }

    public function name(): string
    {
        return 'diskSpace';
    }

    public function health(): HealthInterface
    {
        $space = @\disk_free_space($this->path);

        if (false === $space) {
            return Health::unknown();
        }

        if ($space < $this->threshold) {
            return Health::down()->setDetails(['disk_free_space' => $space, 'threshold' => $this->threshold, 'path' => $this->path]);
        }

        return Health::up()->setDetails(['disk_free_space' => $space, 'threshold' => $this->threshold, 'path' => $this->path]);
    }
}
