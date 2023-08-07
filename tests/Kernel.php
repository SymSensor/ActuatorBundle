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

namespace SymSensor\ActuatorBundle\Tests;

use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use SymSensor\ActuatorBundle\SymSensorActuatorBundle;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    /**
     * @var array<string, mixed>
     */
    private array $actuatorConfig;

    /**
     * @param array<string, mixed> $actuatorConfig
     */
    public function __construct(string $environment, bool $debug, array $actuatorConfig = [])
    {
        parent::__construct($environment, $debug);

        $this->actuatorConfig = $actuatorConfig;
    }

    public function registerBundles(): iterable
    {
        return [
            new FrameworkBundle(),
            new SymSensorActuatorBundle(),
        ];
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $routes->import($this->getProjectDir().'/config/routing.yaml');
    }

    // @phpstan-ignore-next-line
    private function configureContainer(ContainerConfigurator $containerConfigurator, LoaderInterface $loader): void
    {
        $loader->load($this->getProjectDir().'/config/services.yaml');
        $containerConfigurator->extension('sym_sensor_actuator', $this->actuatorConfig);
    }
}
