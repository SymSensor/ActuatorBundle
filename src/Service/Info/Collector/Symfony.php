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

namespace SymSensor\ActuatorBundle\Service\Info\Collector;

use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\HttpKernel\KernelInterface;
use SymSensor\ActuatorBundle\Service\Info\Info;

class Symfony implements Collector
{
    private KernelInterface $kernel;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    public function collect(): Info
    {
        return new Info('symfony', [
            'version' => Kernel::VERSION,
            'lts' => 4 === Kernel::MINOR_VERSION,
            'environment' => $this->kernel->getEnvironment(),
            'endOfMaintenance' => \DateTimeImmutable::createFromFormat('d/m/Y', '01/'.Kernel::END_OF_MAINTENANCE),
            'endOfLife' => \DateTimeImmutable::createFromFormat('d/m/Y', '01/'.Kernel::END_OF_LIFE),
            'bundles' => \array_map(fn(BundleInterface $bundle): string => $bundle::class, $this->kernel->getBundles()),
        ]);
    }
}
