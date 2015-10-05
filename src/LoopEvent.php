<?php
namespace Rubicon\Loop;

use Zend\EventManager\Event;

/**
 * @method LoopInterface getTarget
 */
class LoopEvent extends Event
{
    const EVENT_EXECUTE_PRE   = 'loop.callback.pre';
    const EVENT_EXECUTE_POST  = 'loop.callback.post';
    const EVENT_EXECUTE_ERROR = 'loop.callback.error';
    const EVENT_LOOP_START    = 'loop.start';
    const EVENT_LOOP_STOP     = 'loop.stop';

    /**
     * @var \Exception
     */
    private $exception;

    /**
     * Loop execution result
     *
     * @var mixed
     */
    private $result;

    /**
     * @var LoopConfig
     */
    private $config;

    /**
     * @param LoopInterface $loop
     * @param LoopConfig    $config
     */
    public function __construct(LoopInterface $loop, LoopConfig $config)
    {
        $this->target = $loop;
        $this->config = $config;
    }

    /**
     * @param \Exception $exception
     * @return $this
     */
    public function setException(\Exception $exception)
    {
        $this->exception = $exception;

        return $this;
    }

    /**
     * @return \Exception
     */
    public function getException()
    {
        return $this->exception;
    }

    /**
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param mixed $result
     * @return $this
     */
    public function setResult($result)
    {
        $this->result = $result;

        return $this;
    }

    /**
     * @return LoopConfig
     */
    public function getConfig()
    {
        return $this->config;
    }
}