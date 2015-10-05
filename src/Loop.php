<?php
namespace Rubicon\Loop;

use Rubicon\Loop\Listener\Priority;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerInterface;
use Rubicon\Loop\Exception\LoopException;

class Loop implements LoopInterface
{
    /**
     * @var EventManagerInterface
     */
    private $emitter;

    /**
     * @var LoopConfig
     */
    private $config;

    /**
     * @var bool
     */
    private $running;

    /**
     * @var LoopEvent
     */
    private $event;

    /**
     * @param EventManagerInterface $emitter
     * @param array $config
     */
    public function __construct($config = [], EventManagerInterface $emitter = null)
    {
        $this->emitter = $emitter ?: new EventManager();
        $this->config  = $config instanceof LoopConfig ? $config : new LoopConfig($config);
        $this->event   = new LoopEvent($this, $this->config);
        $this->running = false;

        $this->attachListeners();
    }

    /**
     * @param callable $callback
     */
    public function __invoke(callable $callback)
    {
        $this->config->setCallback($callback);
        $this->start();
    }

    /**
     * @param callable $callback
     */
    public function invoke(callable $callback)
    {
        $this($callback);
    }

    /**
     * Proxy method for the event emitter
     *
     * @param string   $event
     * @param callable $callback
     * @param int      $priority
     *
     * @return $this
     */
    public function attach($event, callable $callback = null, $priority = Priority::NORMAL)
    {
        $this->emitter->attach($event, $callback, $priority);

        return $this;
    }

    /**
     * @throws \Exception
     */
    public function start()
    {
        try {
            $this->running = true;
            $this->run($this->config->getCallback());
        } catch (LoopException $exception) {
            $this->event->setException($exception);
            $this->stop();
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function stop()
    {
        $this->running = false;
        $this->emitter->trigger(LoopEvent::EVENT_LOOP_STOP, $this->event);
    }

    /**
     * @return bool
     */
    public function isStopped()
    {
        return false === $this->running;
    }

    /**
     * @param callable $callback
     */
    private function run(callable $callback)
    {
        $event   = $this->event;
        $emitter = $this->emitter;
        $emitter->trigger(LoopEvent::EVENT_LOOP_START, $event);

        while (true) {
            $emitter->trigger(LoopEvent::EVENT_EXECUTE_PRE, $event);
            try {
                $result = call_user_func($callback, $event);
                $name   = LoopEvent::EVENT_EXECUTE_POST;
                $event->setResult($result);
            } catch (\Exception $exception) {
                $name   = LoopEvent::EVENT_EXECUTE_ERROR;
                $event->setException($exception);
            }
            $emitter->trigger($name, $event);
        }
    }

    /**
     * attach listeners from config to event emitter
     */
    private function attachListeners()
    {
        foreach ($this->config->getListeners() as $listener) {
            if (is_string($listener)) {
                $listener = new $listener();
            }
            $listener->attach($this->emitter);
        }
    }
}