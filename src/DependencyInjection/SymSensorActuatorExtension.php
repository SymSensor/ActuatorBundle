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

namespace SymSensor\ActuatorBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use SymSensor\ActuatorBundle\Service\Health\Indicator\DiskSpace;
use SymSensor\ActuatorBundle\Service\Info\Collector\Git;
use SymSensor\ActuatorBundle\Service\Info\Collector\Php;
use SymSensor\ActuatorBundle\Service\Info\Collector\Symfony;

final class SymSensorActuatorExtension extends Extension
{
    /**
     * @param mixed[] $configs
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../../config'));
        $loader->load('services.yaml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $this->processHealthConfiguration($config['health'], $container);
        $this->processInfoConfiguration($config['info'], $container);
    }

    /**
     * @param mixed[] $config
     */
    private function processHealthConfiguration(array $config, ContainerBuilder $container): void
    {
        $enabled = true;
        if (!$this->isConfigEnabled($container, $config)) {
            $enabled = false;
        }

        $container->setParameter('sym_sensor_actuator.health.enabled', $enabled);

        if (\is_array($config['builtin']) && \is_array($config['builtin']['disk_space'])) {
            $diskSpaceConfig = $config['builtin']['disk_space'];
            if (!$this->isConfigEnabled($container, $diskSpaceConfig)) {
                $container->removeDefinition(DiskSpace::class);
            } else {
                $definition = $container->getDefinition(DiskSpace::class);
                $definition->replaceArgument(0, $diskSpaceConfig['path']);
                $definition->replaceArgument(1, $diskSpaceConfig['threshold']);
            }
        }
    }

    /**
     * @param mixed[] $config
     */
    private function processInfoConfiguration(array $config, ContainerBuilder $container): void
    {
        $enabled = true;
        if (!$this->isConfigEnabled($container, $config)) {
            $enabled = false;
        }
        $container->setParameter('sym_sensor_actuator.info.enabled', $enabled);

        if (isset($config['builtin']) && \is_array($config['builtin'])) {
            $builtinMap = [
                'php' => Php::class,
                'symfony' => Symfony::class,
                'git' => Git::class,
            ];
            foreach ($builtinMap as $key => $definition) {
                if (isset($config['builtin'][$key]) && \is_array($config['builtin'][$key]) && !$this->isConfigEnabled($container, $config['builtin'][$key])) {
                    $container->removeDefinition($definition);
                }
            }
        }
    }
}
