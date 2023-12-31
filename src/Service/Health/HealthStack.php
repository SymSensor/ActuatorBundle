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

final class HealthStack implements HealthInterface
{
    /**
     * @var array<string, HealthInterface>
     */
    private array $healthList;

    /**
     * @param array<string, HealthInterface> $healthList
     */
    public function __construct(array $healthList = [])
    {
        $this->healthList = $healthList;
    }

    public function getStatus(): HealthState
    {
        $status = HealthState::UP;
        foreach ($this->healthList as $health) {
            switch ($health->getStatus()) {
                case HealthState::UNKNOWN:
                    $status = HealthState::UNKNOWN;
                    break;
                case HealthState::DOWN:
                    return HealthState::DOWN;
            }
        }

        return $status;
    }

    public function isUp(): bool
    {
        return HealthState::UP === $this->getStatus();
    }

    /**
     * @return array<mixed>
     */
    public function jsonSerialize(): array
    {
        return \array_merge(['status' => $this->getStatus()], $this->healthList);
    }
}
