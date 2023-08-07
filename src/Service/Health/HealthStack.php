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

    public function getStatus(): string
    {
        $status = Health::UP;
        foreach ($this->healthList as $health) {
            $currentKey = \array_search($status, $this->defaultOrder(), true);
            $key = \array_search($health->getStatus(), $this->defaultOrder(), true);

            if (false === $key) {
                $status = Health::UNKNOWN;
            }
            if ($currentKey > $key) {
                $status = $this->defaultOrder()[$key];
            }
        }

        return $status;
    }

    public function isUp(): bool
    {
        return Health::UP === $this->getStatus();
    }

    /**
     * @return array<mixed>
     */
    public function jsonSerialize(): array
    {
        return \array_merge(['status' => $this->getStatus()], $this->healthList);
    }

    /**
     * @return array<int, string>
     */
    private function defaultOrder(): array
    {
        return [
            Health::UNKNOWN,
            Health::DOWN,
            Health::UP,
        ];
    }
}
