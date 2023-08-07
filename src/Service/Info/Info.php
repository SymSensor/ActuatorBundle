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

final class Info implements \JsonSerializable
{
    private string $name;
    /**
     * @var array<mixed>
     */
    private array $informations;

    /**
     * @param array<mixed> $informations
     */
    public function __construct(string $name, array $informations)
    {
        $this->name = $name;
        $this->informations = $informations;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function isEmpty(): bool
    {
        return 0 === \count($this->informations);
    }

    /**
     * @return array<mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->informations;
    }
}
