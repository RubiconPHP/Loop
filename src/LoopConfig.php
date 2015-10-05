<?php
namespace Rubicon\Loop;

use Rubicon\Loop\Exception\LogicException;
use Zend\EventManager\ListenerAggregateInterface;

class LoopConfig
{
    /**
     * @var ListenerAggregateInterface[]
     */
    private static $availableListeners = [
        'interval' => Listener\LoopIntervalListener::class,
        'repeat'   => Listener\LoopRepeatListener::class,
    ];

    /**
     * Milliseconds
     *
     * @var int
     */
    private $interval;

    /**
     * @var int
     */
    private $repeat;

    /**
     * @var callable
     */
    private $callback;

    /**
     * @var ListenerAggregateInterface[]
     */
    private $listeners = [
        Listener\LoopStateListener::class
    ];

    /**
     * Set properties from array keys, and activate appropriated listeners if necessary
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        foreach ($options as $name => $value) {
            if (property_exists($this, $name)) {
                $this->{'set' . $name}($value);
            }
            if (isset(static::$availableListeners[$name])) {
               $this->listeners[] = static::$availableListeners[$name];
            }
        }
    }

    /**
     * @return mixed
     */
    public function getInterval()
    {
        return $this->interval;
    }

    /**
     * @param mixed $interval
     * @return $this
     */
    public function setInterval($interval)
    {
        if (! is_numeric($interval) || ($interval = intval($interval)) < 0) {
            throw new LogicException('INumber of milliseconds must be greater than or equal to 0');
        }
        $this->interval = $interval * 1000;

        return $this;
    }

    /**
     * @return int
     */
    public function getRepeat()
    {
        return $this->repeat;
    }

    /**
     * @param int $repeat
     * @return $this
     */
    public function setRepeat($repeat)
    {
        if (! is_numeric($repeat) || ($repeat = intval($repeat)) < 0) {
            throw new LogicException('Repeat parameters must be greater than or equals to 0');
        }
        $this->repeat = $repeat;

        return $this;
    }

    /**
     * @return callable
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * @param callable $callback
     * @return $this
     */
    public function setCallback(callable $callback)
    {
        $this->callback = $callback;

        return $this;
    }

    /**
     * @return ListenerAggregateInterface[]
     */
    public function getListeners()
    {
        return $this->listeners;
    }

    /**
     * @param array|\Traversable $listeners
     * @return $this
     */
    public function setListeners($listeners)
    {
        foreach ($listeners as $listener) {
            $this->addListener($listener);
        }

        return $this;
    }

    /**
     * @param ListenerAggregateInterface $listener
     * @return $this
     */
    public function addListener($listener)
    {
        if (is_string($listener)) {
            if (! in_array(ListenerAggregateInterface::class, class_implements($listener))) {
                throw new LogicException('Expecting an class that implements ' . ListenerAggregateInterface::class);
            }
        }
        elseif (! $listener instanceof ListenerAggregateInterface) {
            throw new LogicException('Expecting an instance of ' . ListenerAggregateInterface::class);
        }
        $this->listeners[] = $listener;

        return $this;
    }
}