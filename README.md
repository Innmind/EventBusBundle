# EventBusBundle

| `master` | `develop` |
|----------|-----------|
| [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Innmind/EventBusBundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Innmind/EventBusBundle/?branch=master) | [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Innmind/EventBusBundle/badges/quality-score.png?b=develop)](https://scrutinizer-ci.com/g/Innmind/EventBusBundle/?branch=develop) |
| [![Code Coverage](https://scrutinizer-ci.com/g/Innmind/EventBusBundle/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/Innmind/EventBusBundle/?branch=master) | [![Code Coverage](https://scrutinizer-ci.com/g/Innmind/EventBusBundle/badges/coverage.png?b=develop)](https://scrutinizer-ci.com/g/Innmind/EventBusBundle/?branch=develop) |
| [![Build Status](https://scrutinizer-ci.com/g/Innmind/EventBusBundle/badges/build.png?b=master)](https://scrutinizer-ci.com/g/Innmind/EventBusBundle/build-status/master) | [![Build Status](https://scrutinizer-ci.com/g/Innmind/EventBusBundle/badges/build.png?b=develop)](https://scrutinizer-ci.com/g/Innmind/EventBusBundle/build-status/develop) |

Symfony integration of `innmind/event-bus` that ease stacking event buses.

## Installation

```sh
composer require innmind/event-bus-bundle
```

In your `AppKernel.php` add the following line:
```php
//app/AppKernel.php

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...
            new Innmind\EventBusBundle\InnmindEventBusBundle,
        );
        // ...
    }
    // ...
}
```

## Usage

```php
$container->get('innmind_event_bus')->dispatch(new MyEvent);
```

In order to dispatch your events you need to define the listeners as services with the tag `innmind_event_bus.listener` with the attribte `listen_to` that will contain the command FQCN.
