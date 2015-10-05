<?php
namespace Rubicon\Loop\Listener;

use Rubicon\Loop\Exception\LoopException;
use Rubicon\Loop\LoopConfig;
use Rubicon\Loop\LoopEvent;
use Rubicon\Loop\LoopInterface;
use Zend\EventManager\EventManagerInterface;

class LoopRepeatListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $events;

    /**
     * @var LoopRepeatListener
     */
    private $instance;

    public function setUp()
    {
        $this->instance = new LoopRepeatListener();
        $this->events   = $this->getMock(EventManagerInterface::class);
    }

    public function testAttach()
    {
        $this->events
            ->expects($this->exactly(3))
            ->method('attach')
            ->withConsecutive(
                [LoopEvent::EVENT_LOOP_START, [$this->instance, 'clearExecutions'], Priority::NORMAL],
                [LoopEvent::EVENT_EXECUTE_POST, [$this->instance, 'checkExecutions'], Priority::VERY_LOW],
                [LoopEvent::EVENT_EXECUTE_ERROR, [$this->instance, 'checkExecutions'], Priority::VERY_LOW]
            )
        ;

        $this->instance->attach($this->events);
    }

    public function testClearExecutions()
    {
        $this->assertNull($this->instance->clearExecutions());
        $this->assertAttributeEquals(0, 'executions', $this->instance);
    }

    public function testcheckExecutions()
    {
        $config = new LoopConfig();
        $event  = new LoopEvent($this->getMock(LoopInterface::class), $config);
        $config->setRepeat(10);
        $this->assertNull($this->instance->checkExecutions($event));
        $this->assertAttributeEquals(1, 'executions', $this->instance);

        $this->setExpectedException(LoopException::class);
        $config->setRepeat(1);
        $this->instance->checkExecutions($event);
    }
}
