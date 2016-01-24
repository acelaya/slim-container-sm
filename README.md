# Slim Container - ServiceManager

[![Build Status](https://travis-ci.org/acelaya/slim-container-sm.svg)](https://travis-ci.org/acelaya/slim-container-sm)
[![Code Coverage](https://scrutinizer-ci.com/g/acelaya/slim-container-sm/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/acelaya/slim-container-sm/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/acelaya/slim-container-sm/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/acelaya/slim-container-sm/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/acelaya/slim-container-sm/v/stable.png)](https://packagist.org/packages/acelaya/slim-container-sm)
[![Total Downloads](https://poser.pugx.org/acelaya/slim-container-sm/downloads.png)](https://packagist.org/packages/acelaya/slim-container-sm)
[![License](https://poser.pugx.org/acelaya/slim-container-sm/license.png)](https://packagist.org/packages/acelaya/slim-container-sm)

A Slim framework v2 container wrapping a Zend ServiceManager v3 so that services can be fetched from it.

> Current stable release depends on version 3 of the ServiceManager. If you need to use the version 2, install v1 of this component.

### Installation

Install it with composer. Just run this.

    composer require acelaya/slim-container-sm

### Usage

This library provides a simple class, the `Acelaya\SlimContainerSm\Container` which extends `Slim\Helper\Set` and wraps an instance of a ServiceManager (which can be injected on it or lazy loaded).

By replacing Slim's container object by this one you can make Slim framework to fetch services from the ServiceManager, which is much more advanced and configurable.

```php
use Acelaya\SlimContainerSm\Container;
use Slim\Slim;
use Vendor\ComplexClass;
use Vendor\MyAbstractFactory;
use Zend\ServiceManager\Factory\InvokableFactory;
use Zend\ServiceManager\ServiceManager;

// Create a ServiceManager that is going to be used by the application and add some services to it
$sm = new ServiceManager([
    'factories' => [
        'complex_service' => function ($sm) {
            // Do stuff...
            
            return new ComplexClass();
        },
        'foo_invokable' => InvokableFactory::class,
    ],
    'abstract_factories' => [
        MyAbstractFactory::class
    ],
    'aliases' => [
        'foo' => 'foo_invokable'
    ]
]);
// Inject the ServiceManager in the new container
$container = new Container($sm);

// Create Slim object which will initialize its container
$app = new Slim();
// Inject default Slim services into our container
$container->consumeSlimContainer($app->container);
// Override Slim's container with the new one
$app->container = $container;
```

Once this is done, Slim will continue working with the new container as its own, and you can create more complex services using the ServiceManager.

This library is very useful with [rka-slim-controller](https://github.com/akrabat/rka-slim-controller) and [slimcontroller](https://github.com/fortrabbit/slimcontroller), both of them libraries that allow to create controllers in Slim framework as a service. In combination with this, you will be able to register Controllers in a ServiceManager.

### Invalid methods

Because of the way the ServiceManager works, there are methods in `Slim\Helper\Set` that can't be used. These are the methods `all`, `keys`, `count` and `getIterator`.
Those methods will throw a `Acelaya\SlimContainerSm\Exception\BadMethodCallException`.
This makes the `Acelaya\SlimContainerSm\Container` not iterable.
