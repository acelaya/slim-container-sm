<?php
namespace Acelaya\SlimContainerSm\Factory;

use Acelaya\SlimContainerSm\Container;

/**
 * This is used to wrapp services created as a singleton,
 * this way a SM factory can be created that returns the result of the original factory
 * @author
 * @link
 */
class SingletonWrapper
{
    /**
     * @var Container
     */
    protected $container;
    /**
     * @var callable
     */
    protected $originalFactory;

    public function __construct(Container $container, callable $originalFactory)
    {
        $this->container = $container;
        $this->originalFactory = $originalFactory;
    }

    public function __invoke($sm)
    {
        return call_user_func($this->originalFactory, $this->container);
    }
}
