<?php
namespace Rubicon\Loop\Listener;

use Rubicon\Loop\Exception\LoopException;
use Rubicon\Loop\LoopConfig;
use Rubicon\Loop\LoopEvent;
use Rubicon\Loop\LoopInterface;
use Zend\EventManager\EventManagerInterface;

class LoopStateListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $events;

    /**
     * @var LoopStateListener
     */
    private $instance;

    public function setUp()
    {
        $this->instance = new LoopStateListener();
        $this->events   = $this->getMock(EventManagerInterface::class);
    }

    public function testAttach()
    {
        $this->events
            ->expects($this->once())
            ->method('attach')
            ->with(LoopEvent::EVENT_EXECUTE_PRE, [$this->instance, 'checkStatus'], Priority::VERY_LOW)
        ;

        $this->instance->attach($this->events);
    }

    public function testCheckStatus()
    {
        $event = new LoopEvent($loop = $this->getMock(LoopInterface::class), new LoopConfig());
        $loop
            ->expects($this->exactly(2))
            ->method('isStopped')
            ->willReturnOnConsecutiveCalls(false, true)
        ;

        $this->instance->checkStatus($event);
        $this->setExpectedException(LoopException::class);
        $this->instance->checkStatus($event);
    }
}
