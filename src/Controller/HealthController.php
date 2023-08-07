<?php

declare(strict_types=1);

namespace SymSensor\ActuatorBundle\Controller;

use SymSensor\ActuatorBundle\Service\Health\HealthIndicatorStack;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class HealthController extends AbstractController
{
    private HealthIndicatorStack $healthIndicatorStack;

    public function __construct(HealthIndicatorStack $healthIndicatorStack)
    {
        $this->healthIndicatorStack = $healthIndicatorStack;
    }

    public function health(): JsonResponse
    {
        if ($this->getParameter('actuator.health.enabled') === false) {
            throw new NotFoundHttpException();
        }

        $healthStack = $this->healthIndicatorStack->check();

        return new JsonResponse(
            $healthStack,
            $healthStack->isUp() ? 200 : 503,
        );
    }
}