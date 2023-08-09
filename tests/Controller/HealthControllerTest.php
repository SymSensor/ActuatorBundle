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

namespace SymSensor\ActuatorBundle\Tests\Controller;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use SymSensor\ActuatorBundle\Service\Health\HealthState;

class HealthControllerTest extends ControllerTestCase
{
    /**
     * @test
     */
    public function healthEndpointWillReturnNotFoundIfNotEnabled(): void
    {
        $this->rebootKernelWithConfig(['health' => ['enabled' => false]]);

        self::expectException(NotFoundHttpException::class);
        $this->client->catchExceptions(false);

        $this->client->request('GET', '/health');

        $this->client->getResponse();
    }

    /**
     * @test
     */
    public function healthEndpoint(): void
    {
        $this->client->request('GET', '/health');

        $response = $this->client->getResponse();

        self::assertEquals(200, $response->getStatusCode());
        $response = \json_decode((string) $response->getContent(), true, 512, \JSON_THROW_ON_ERROR);
        self::assertIsArray($response);
        self::assertArrayHasKey('status', $response);
        self::assertEquals(HealthState::UP, $response['status']);
    }
}
