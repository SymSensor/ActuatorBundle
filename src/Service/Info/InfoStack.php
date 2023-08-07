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

class InfoStack implements \JsonSerializable
{
    /**
     * @var iterable<Info>
     */
    private iterable $infos;

    /**
     * @param iterable<Info> $infos
     */
    public function __construct(iterable $infos)
    {
        $this->infos = $infos;
    }

    /**
     * @return mixed[]
     */
    public function jsonSerialize(): array
    {
        $data = [];
        foreach ($this->infos as $info) {
            if ($info->isEmpty()) {
                continue;
            }

            $data[$info->name()] = $info->jsonSerialize();
        }

        return $data;
    }
}
