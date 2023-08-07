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
use SymSensor\ActuatorBundle\Service\Health\HealthIndicatorStack;

class HealthController extends AbstractController
{
    private HealthIndicatorStack $healthIndicatorStack;

    public function __construct(HealthIndicatorStack $healthIndicatorStack)
    {
        $this->healthIndicatorStack = $healthIndicatorStack;
    }

    public function health(): JsonResponse
    {
        if (false === $this->getParameter('sym_sensor_actuator.health.enabled')) {
            throw new NotFoundHttpException();
        }

        $healthStack = $this->healthIndicatorStack->check();

        return new JsonResponse(
            $healthStack,
            $healthStack->isUp() ? 200 : 503,
        );
    }
}
