<?php
namespace Acelaya\SlimContainerSm\Test;

use Acelaya\SlimContainerSm\Container;
use PHPUnit_Framework_TestCase as TestCase;
use Slim\Helper\Set;
use Zend\ServiceManager\ServiceManager;

class ContainerTest extends TestCase
{
    /**
     * @var Container
     */
    private $container;
    /**
     * @var ServiceManager
     */
    private $sm;

    public function setUp()
    {
        $this->sm = new ServiceManager();
        $this->container = new Container($this->sm);
    }

    public function testServiceManagerAware()
    {
        $this->assertSame($this->sm, $this->container->getServiceManager());
        $anotherSm = new ServiceManager();
        $this->container->setServiceManager($anotherSm);
        $this->assertSame($anotherSm, $this->container->getServiceManager());
    }

    public function testSet()
    {
        $expected = new \stdClass();
        $this->container->set('foo', $expected);
        $this->assertSame($expected, $this->sm->get('foo'));

        $this->container->set('bar', function () {
            return new \stdClass();
        });
        $this->assertInstanceOf('stdClass', $this->sm->get('bar'));
    }

    public function testGet()
    {
        // Via container
        $this->container->foo = [];
        $this->assertEquals([], $this->container->get('foo'));

        // Via service manager
        $this->sm->setService('foo', new \stdClass());
        $this->sm->setAlias('bar', 'foo');
        $this->assertInstanceOf('stdClass', $this->container->get('bar'));

        $this->sm->setFactory('factory', function (ServiceManager $sm) {
            return $sm->get('bar');
        });
        $this->assertInstanceOf('stdClass', $this->container->get('factory'));

        $this->sm->setInvokableClass('invokable', 'stdClass');
        $this->assertInstanceOf('stdClass', $this->container->get('invokable'));
    }

    /**
     * @expectedException \Acelaya\SlimContainerSm\Exception\BadMethodCallException
     */
    public function testAllThrowsException()
    {
        $this->container->all();
    }

    public function testKeys()
    {
        $this->sm->setService('foo', new \stdClass());
        $this->sm->setService('bar', new \stdClass());
        $this->sm->setService('baz', new \stdClass());
        $this->assertCount(3, $this->container->keys());

        $this->sm->setInvokableClass('foobar', 'stdClass');
        $this->sm->setInvokableClass('barfoo', 'stdClass');
        $this->assertCount(5, $this->container->keys());
    }

    public function testHas()
    {
        $this->assertFalse($this->container->has('foo'));
        $this->sm->setService('foo', null);
        $this->assertFalse($this->container->has('foo'));
        $this->container->foo = 'Hello!';
        $this->assertTrue($this->container->has('foo'));
    }

    public function testRemove()
    {
        $this->container->foo = [];
        $this->assertTrue($this->container->has('foo'));
        $this->container->remove('foo');
        $this->assertFalse($this->container->has('foo'));
    }

    public function testClear()
    {
        $this->container->foo = new \stdClass();
        $this->container->bar = new \stdClass();
        $this->container->clear();
        $this->assertFalse($this->container->has('foo'));
        $this->assertFalse($this->container->has('bar'));
    }

    public function testCount()
    {
        $this->container->foo = new \stdClass();
        $this->container->bar = new \stdClass();
        $this->sm->setService('baz', new \stdClass());
        $this->sm->setAlias('alias', 'baz');
        $this->sm->setFactory('factory', function ($sm) {

        });
        $this->assertCount(5, $this->container);
    }

    public function testGetIterator()
    {
        $iterator = $this->container->getIterator();
        $this->assertInstanceOf('ArrayIterator', $iterator);
        $this->assertCount(0, $iterator);
    }

    public function testSignleton()
    {
        $expected = new \stdClass();
        $this->container->singleton('foo', $expected);
        $this->sm->has('foo');
        $this->assertSame($expected, $this->container->get('foo'));
        $this->assertSame($expected, $this->sm->get('foo'));
    }

    public function testSingletonWithCallable()
    {
        $expected = new \stdClass();
        $this->container->singleton('foo', function () use ($expected) {
            return $expected;
        });
        $this->assertSame($expected, $this->container->get('foo'));
    }

    public function testConsumeSlimContainer()
    {
        $anoterContainer = new Set();
        $anoterContainer->foo = [];
        $anoterContainer->bar = new \stdClass();
        $anoterContainer->baz = function ($c) {
            return 'Hello';
        };
        $anoterContainer->singleton('foobar', function ($c) {
            return 'Hello';
        });
        $anoterContainer->barfoo = [$this, 'fakeMathod'];
        $this->container->consumeSlimContainer($anoterContainer);

        $this->assertTrue($this->sm->has('foo'));
        $this->assertTrue($this->container->has('foo'));
        $this->assertTrue($this->sm->has('bar'));
        $this->assertTrue($this->container->has('bar'));
        $this->assertTrue($this->sm->has('baz'));
        $this->assertTrue($this->container->has('baz'));
        $this->assertTrue($this->sm->has('foobar'));
        $this->assertTrue($this->container->has('foobar'));
        $this->assertTrue($this->sm->has('barfoo'));
        $this->assertTrue($this->container->has('barfoo'));
    }

    public function fakeMathod()
    {

    }
}
