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

namespace SymSensor\ActuatorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use SymSensor\ActuatorBundle\Service\Info\InfoCollectorStack;

class InfoController extends AbstractController
{
    private InfoCollectorStack $collector;

    public function __construct(InfoCollectorStack $collector)
    {
        $this->collector = $collector;
    }

    public function info(): JsonResponse
    {
        if (false === $this->getParameter('sym_sensor_actuator.info.enabled')) {
            throw new NotFoundHttpException();
        }

        return new JsonResponse($this->collector->collect());
    }
}
