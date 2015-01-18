<?php
namespace Acelaya\SlimContainerSm\Test\Factory;

use Acelaya\SlimContainerSm\Container;
use Acelaya\SlimContainerSm\Factory\SingletonWrapper;
use PHPUnit_Framework_TestCase as TestCase;

class SingletonWrapperTest extends TestCase
{
    /**
     * @var SingletonWrapper
     */
    private $wrapper;

    public function testInvoke()
    {
        $expected = new \stdClass();
        $originalFactory = function () use ($expected) {
            return $expected;
        };
        $container = new Container();
        $this->wrapper = new SingletonWrapper($container, $originalFactory);
        $this->assertSame($expected, call_user_func($this->wrapper, $container));
    }
}
