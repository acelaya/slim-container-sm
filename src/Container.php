<?php
namespace Acelaya\SlimContainerSm;

use Acelaya\SlimContainerSm\Exception\BadMethodCallException;
use Acelaya\SlimContainerSm\Factory\CallbackWrapper;
use Slim\Helper\Set;
use Zend\ServiceManager\ServiceManager;

/**
 * Class Container
 * @author Alejandro Celaya Alastrué
 * @link http://www.alejandrocelaya.com
 */
class Container extends Set implements
    ServiceManagerAwareInterface,
    SlimContainerConsumibleInterface
{
    /**
     * @var ServiceManager
     */
    protected $sm;

    public function __construct(ServiceManager $sm = null)
    {
        $this->setServiceManager($sm ?: new ServiceManager());
    }

    /**
     * Set data key to value
     * @param string $key   The data key
     * @param mixed  $value The data value
     */
    public function set($key, $value)
    {
        if (is_callable($value)) {
            $this->registerFactory($key, $value, false);
        } else {
            $this->sm->setService($key, $value);
        }
    }

    /**
     * Get data value with key
     * @param  string $key     The data key
     * @param  mixed  $default The value to return if data key does not exist
     * @return mixed           The data value, or the default value
     */
    public function get($key, $default = null)
    {
        return $this->has($key) ? $this->sm->get($key) : $default;
    }

    /**
     * Fetch set data
     * @return array This set's key-value data array
     */
    public function all()
    {
        throw new BadMethodCallException('It is not possible to fetch all services registered in a ServiceManager');
    }

    /**
     * Fetch set data keys
     * @return array This set's key-value data array keys
     */
    public function keys()
    {
        return array_values($this->sm->getCanonicalNames());
    }

    /**
     * Does this set contain a key?
     * @param  string  $key The data key
     * @return boolean
     */
    public function has($key)
    {
        return $this->sm->has($key);
    }

    /**
     * Remove value with key from this set
     * @param string $key The data key
     */
    public function remove($key)
    {
        $this->sm->setService($key, null);
    }

    /**
     * Clear all values
     */
    public function clear()
    {
        $services = $this->keys();
        foreach ($services as $service) {
            $this->set($service, null);
        }
    }

    /**
     * Countable
     */
    public function count()
    {
        return count($this->keys());
    }

    /**
     * IteratorAggregate
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->keys());
    }

    /**
     * Ensure a value or object will remain globally unique
     * @param string $key The value or object name
     * @param $value
     * @return mixed
     */
    public function singleton($key, $value)
    {
        if (is_callable($value)) {
            $this->registerFactory($key, $value);
        } else {
            $this->sm->setService($key, $value);
        }
    }

    /**
     * @param ServiceManager $sm
     * @return mixed
     */
    public function setServiceManager(ServiceManager $sm)
    {
        $this->sm = $sm;
        $this->sm->setAllowOverride(true);
    }

    /**
     * @return ServiceManager
     */
    public function getServiceManager()
    {
        return $this->sm;
    }

    /**
     * Makes this to consume the services defined in provided container
     *
     * @param Set $container
     * @return mixed
     */
    public function consumeSlimContainer(Set $container)
    {
        foreach ($container as $key => $value) {
            if ($value instanceof \Closure) {
                // Try to determin if this belongs to a singleton or not
                $refFunc = new \ReflectionFunction($value);
                // Slim singletons have a static 'object' variable
                $shared = in_array('object', $refFunc->getStaticVariables());
                $this->registerFactory($key, $value, $shared);
            } elseif (is_callable($value)) {
                // Register as non-shared factories any other callable
                $this->registerFactory($key, $value, false);
            } else {
                $this->sm->setService($key, $value);
            }
        }
    }

    /**
     * Registers a factory wrapping a Slim factory into a ZF2 factory
     *
     * @param $key
     * @param callable $callable
     * @param bool $shared
     */
    protected function registerFactory($key, callable $callable, $shared = true)
    {
        $this->sm->setFactory($key, new CallbackWrapper($this, $callable));
        $this->sm->setShared($key, $shared);
    }
}
