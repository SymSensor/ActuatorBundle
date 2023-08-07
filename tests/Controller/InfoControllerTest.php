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

class InfoControllerTest extends ControllerTestCase
{
    /**
     * @test
     */
    public function infoEndpointWillReturnNotFoundIfNotEnabled(): void
    {
        $this->rebootKernelWithConfig(['info' => ['enabled' => false]]);
        self::expectException(NotFoundHttpException::class);
        $this->client->catchExceptions(false);
        $this->client->request('GET', '/info');

        $this->client->getResponse();
    }

    /**
     * @test
     */
    public function infoEndpoint(): void
    {
        $this->client->request('GET', '/info');

        $response = $this->client->getResponse();

        self::assertEquals(200, $response->getStatusCode());
        $json = \json_decode((string) $response->getContent(), true, 512, \JSON_THROW_ON_ERROR);

        self::assertIsArray($json);
        self::assertArrayHasKey('php', $json);
        self::assertArrayHasKey('symfony', $json);
        self::assertArrayHasKey('git', $json);
    }
}
