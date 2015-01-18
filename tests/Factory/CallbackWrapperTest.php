<?php
namespace Acelaya\SlimContainerSm\Test\Factory;

use Acelaya\SlimContainerSm\Container;
use Acelaya\SlimContainerSm\Factory\CallbackWrapper;
use PHPUnit_Framework_TestCase as TestCase;

class SingletonWrapperTest extends TestCase
{
    /**
     * @var CallbackWrapper
     */
    private $wrapper;

    public function testInvoke()
    {
        $expected = new \stdClass();
        $originalFactory = function () use ($expected) {
            return $expected;
        };
        $container = new Container();
        $this->wrapper = new CallbackWrapper($container, $originalFactory);
        $this->assertSame($expected, call_user_func($this->wrapper, $container));
    }
}
