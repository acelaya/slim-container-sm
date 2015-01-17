# Slim Container - ServiceManager

A Slim framework container wrapping a ZF2 ServiceManager so that services can be pulled from it.

### Installation

Install it with composer. Just run this.

    composer require acelaya/slim-container-sm:~0.1

### Usage

This library provides a simple class, the `Acelaya\SlimContainerSm\Container` which extends `Slim\Helper\Set` and wraps an instance of a ServiceManager (which can be injected on it or lazy loaded).

By replacing Slim's container object by this one you can make Slim framework to fetch services from the ServiceManager, which is much more advanced and configurable.
