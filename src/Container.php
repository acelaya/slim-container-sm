<?php
namespace Acelaya\SlimContainerSm;

use Acelaya\SlimContainerSm\Exception\BadMethodCallException;
use Slim\Helper\Set;
use Zend\ServiceManager\ServiceManager;

/**
 * Class Container
 * @author Alejandro Celaya Alastrué
 * @link http://www.alejandrocelaya.com
 */
class Container extends Set
{
    /**
     * @var ServiceManager
     */
    protected $sm;

    public function __construct(ServiceManager $sm = null)
    {
        $this->sm = $sm ?: new ServiceManager();
        $this->sm->setAllowOverride(true);
    }

    /**
     * Set data key to value
     * @param string $key   The data key
     * @param mixed  $value The data value
     */
    public function set($key, $value)
    {
        $this->sm->setService($key, $value);
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
        $services = $this->sm->getRegisteredServices();
        return array_merge($services['invokableClasses'], $services['factories'], $services['aliases'], $services['instances']);
    }

    /**
     * Does this set contain a key?
     * @param  string  $key The data key
     * @return boolean
     */
    public function has($key)
    {
        if (! $this->sm->has($key)) {
            return false;
        }

        $service = $this->sm->get($key);
        return isset($service);
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
        $this->data = array();
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
     * @param Callable $value The closure that defines the object
     * @return mixed
     */
    public function singleton($key, $value)
    {
        // Create a service and force it to be shared
        $this->set($key, $value);
        $this->sm->setShared($key, true);
    }

    /**
     * Protect closure from being directly invoked
     * @param Callable $callable A closure to keep from being invoked and evaluated
     * @return Callable
     */
    public function protect(\Closure $callable)
    {
        throw new BadMethodCallException(sprintf('Method %s not applicable in the scope of a ServiceManager', __METHOD__));
    }
}
