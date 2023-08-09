# ActuatorBundle

<img src="https://github.com/SymSensor/ActuatorBundle/blob/main/docs/logo.png?raw=true" align="right" width="250"/>

ActuatorBundle provides basically two features:

- It provides an endpoint which can be used as a health probe url
- With another endpoint you can read runtime informations about the deployed software

It is also easily extensible, so you add your custom logic to both features. Some predefined extensions can be also found within this [GitHub organization](https://github.com/SymSensor).

The bundle is heavily inspired by [akondas/symfony-actuator-bundle](https://github.com/akondas/symfony-actuator-bundle).

## Installation

Make sure Composer is installed globally, as explained in the
[installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

### Applications that use Symfony Flex

Open a command console, enter your project directory and execute:

```console
$ composer require symsensor/actuator-bundle
```

### Applications that don't use Symfony Flex

#### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require symsensor/actuator-bundle
```

#### Step 2: Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles
in the `config/bundles.php` file of your project:

```php
// config/bundles.php

return [
    // ...
    SymSensor\ActuatorBundle\SymSensorActuatorBundle::class => ['all' => true],
];
```

After the installation you have to configure the routing in your `config/routes.yaml` file:

```yaml
actuator:
  resource: '@SymSensorActuatorBundle/config/routing.yaml'
  prefix: /_actuator
```

The routing defines two endpoints: 

- `<prefix>/health`
- `<prefix>/info`

You should secure the two endpoints with the builtin [Security](https://symfony.com/doc/current/security.html) so that the two endpoints are not public accessible. 

## Configuration

The Bundle can be configured with a configuration file named `config/packages/sym_sensor_actuator.yaml`. Following snippet shows the default value for all configurations:

```yaml
actuator:
  health:
    enabled: true
    builtin:
      disk_space:
        enabled: true
        threshold: 52428800
        path: '%kernel.project_dir%'
  info:
    enabled: true
    builtin:
      php:
        enabled: true
      symfony:
        enabled: true
      git:
        enabled: true
```

Following table outlines the configuration:

| key                                   | default                | description                                                                   |
| ------------------------------------- | ---------------------- | ----------------------------------------------------------------------------- |
| actuator.health.enabled               | true                   | if the health endpoint should be enabled                                      |
| actuator.health.disk_space.enabled    | true                   | if the builtin disk_space health endpoint should be enabled                   |
| actuator.health.disk_space.threshold  | 52428800               | Size in bytes which has to be free in order that this health endpoint is "UP" |
| actuator.health.disk_space.path       | '%kernel.project_dir%' | The directory which should be monitored                                       |
| actuator.info.enabled                 | true                   | if the info endpoint should be enabled                                        |
| actuator.info.builtin.php.enabled     | true                   | if the php info endpoint should be enabled                                    |
| actuator.info.builtin.symfony.enabled | true                   | if the symfony info endpoint should be enabled                                |
| actuator.info.builtin.git.enabled     | true                   | if the git info endpoint should be enabled                                    |


## Extending

### Health indicator

You can write your own health indicator and implement your own logic to determine the state of your application. To do so, you have to implement the interface `HealthIndicator` and tag your service with the tag `sym_sensor_actuator.health_indicator`.

So for example, add following class under `src/Health/CustomHealthIndicator.php`:

```php
<?php

declare(strict_types=1);

namespace App\Health;

use SymSensor\ActuatorBundle\Service\Health\HealthIndicator;
use SymSensor\ActuatorBundle\Service\Health\Health;

class CustomHealthIndicator implements HealthIndicator
{
    public function name(): string
    {
        return 'custom';
    }

    public function health(): Health
    { 
        return Health::up()->setDetails(['state' => 'OK!']);
    }
}
```

Then add following definition to `config/services.yaml`:

```yaml
services:
  App\Health\CustomHealthIndicator: 
    tags: ['sym_sensor_actuator.health_indicator']
```

### Information Collector

Similar to a health indicator, you can write also a service which exposes informations. To do so, you have to implement the interface `Collector` and add the tag `sym_sensor_actuator.info_collector`.

```php
<?php

declare(strict_types=1);

namespace App\Info;

use SymSensor\ActuatorBundle\Service\Info\Collector\Collector;
use SymSensor\ActuatorBundle\Service\Info\Info;

class CustomInfoCollector implements Collector
{
    public function collect(): Info
    {
        return new Info('my-info', [ 'time' => time() ]);
    }
}
```

Then add following definition to `config/services.yaml`:

```yaml
services:
  App\Info\CustomInfoCollector: 
    tags: ['sym_sensor_actuator.info_collector']
```

## License

ActuatorBundle is released under the MIT Licence. See the bundled LICENSE file for details.

## Author

Originally developed by [Arkadiusz Kondas](https://twitter.com/ArkadiuszKondas)
