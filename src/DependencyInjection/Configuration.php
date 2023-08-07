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

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('sym_sensor_actuator');

        $rootNode = $treeBuilder->getRootNode();

        $rootNode // @phpstan-ignore-line
            ->children()
                ->arrayNode('health')
                    ->canBeDisabled()
                    ->children()
                        ->arrayNode('builtin')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('disk_space')
                                    ->addDefaultsIfNotSet()
                                    ->canBeDisabled()
                                    ->children()
                                        ->integerNode('threshold')->defaultValue(50 * 1024 * 1024)->end()
                                        ->scalarNode('path')->defaultValue('%kernel.project_dir%')->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('info')
                    ->addDefaultsIfNotSet()
                    ->canBeDisabled()
                    ->children()
                        ->arrayNode('builtin')
                            ->children()
                                ->arrayNode('php')
                                    ->canBeDisabled()
                                ->end()
                                ->arrayNode('symfony')
                                    ->canBeDisabled()
                                ->end()
                                ->arrayNode('git')
                                    ->canBeDisabled()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
