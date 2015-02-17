# Slim Container - ServiceManager

[![Build Status](https://travis-ci.org/acelaya/slim-container-sm.svg)](https://travis-ci.org/acelaya/slim-container-sm)
[![Code Coverage](https://scrutinizer-ci.com/g/acelaya/slim-container-sm/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/acelaya/slim-container-sm/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/acelaya/slim-container-sm/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/acelaya/slim-container-sm/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/acelaya/slim-container-sm/v/stable.png)](https://packagist.org/packages/acelaya/slim-container-sm)
[![Total Downloads](https://poser.pugx.org/acelaya/slim-container-sm/downloads.png)](https://packagist.org/packages/acelaya/slim-container-sm)
[![License](https://poser.pugx.org/acelaya/slim-container-sm/license.png)](https://packagist.org/packages/acelaya/slim-container-sm)

A Slim framework container wrapping a ZF2 ServiceManager so that services can be fetched from it.

### Installation

Install it with composer. Just run this.

    composer require acelaya/slim-container-sm:~0.1

### Usage

This library provides a simple class, the `Acelaya\SlimContainerSm\Container` which extends `Slim\Helper\Set` and wraps an instance of a ServiceManager (which can be injected on it or lazy loaded).

By replacing Slim's container object by this one you can make Slim framework to fetch services from the ServiceManager, which is much more advanced and configurable.

```php
// Create a ServiceManager that is going to be used by the application and add some services to it
$sm = new \Zend\ServiceManager\ServiceManager(new \Zend\ServiceManager\Config([
    'invokables' => [
        'foo' => 'Vendor\MyClass',
        'bar' => 'Vendor\AnotherClass'
    ],
    'factories' => [
        'complex_service' => function ($sm) {
            // Do stuff...
            
            return new \Vendor\ComplexClass();
        }
    ]
]));
// Inject the ServiceManager in the new container
$container = new \Acelaya\SlimContainerSm\Container($sm);

// Create Slim object which will initialize its container
$app = new \Slim\Slim();
// Inject default Slim services into our container
$container->consumeSlimContainer($app->container);
// Override Slim's container with the new one
$app->container = $container;
```

Once this is done, Slim will continue working with the new container as its own, and you can create more complex services using the ServiceManager.

This library is very useful with [slimcontroller](https://github.com/fortrabbit/slimcontroller), a library which allows to create controllers in Slim framework as a service. In combination with this, you will be able to register Controllers in a ServiceManager.

### Invalid methods

Because of the way the ServiceManager works, there is one method in `Slim\Helper\Set` that can't be used, the method `all`. That method will throw a `Acelaya\SlimContainerSm\Exception\BadMethodCallException`.
