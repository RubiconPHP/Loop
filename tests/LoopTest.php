<?php
namespace Rubicon\Loop;

use Rubicon\Loop\Exception\LoopException;
use Zend\EventManager\EventManagerInterface;

class LoopTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $events;

    public function setUp()
    {
        $this->events = $this->getMock(EventManagerInterface::class);
    }

    public function testImplementsLoopInterface()
    {
        $this->assertInstanceOf(LoopInterface::class, new Loop);
    }

    public function testLoopCatchLoopException()
    {
        $instance = new Loop([], $this->events);
        $this->events
             ->expects($this->at(0))
             ->method('trigger')
             ->with(LoopEvent::EVENT_LOOP_START)
        ;
        $this->events
             ->expects($this->at(1))
             ->method('trigger')
             ->with(LoopEvent::EVENT_EXECUTE_PRE)
        ;
        $this->events
             ->expects($this->at(2))
             ->method('trigger')
             ->with(LoopEvent::EVENT_EXECUTE_POST)
            ->willThrowException(new LoopException())
        ;
        $this->events
             ->expects($this->at(3))
             ->method('trigger')
             ->with(LoopEvent::EVENT_LOOP_STOP)
        ;

        $instance->invoke('is_object');
    }

    public function testLoopThrowException()
    {
        $instance = new Loop([], $this->events);
        $this->events
            ->expects($this->at(2))
            ->method('trigger')
            ->with(LoopEvent::EVENT_EXECUTE_ERROR)
            ->willThrowException(new \RuntimeException())
        ;

        $this->setExpectedException(\RuntimeException::class);
        $instance(function(){
            throw new \Exception;
        });
    }

    public function testIsStopped()
    {
        $instance = new Loop([], $this->events);
        $this->assertTrue($instance->isStopped());
    }

    public function testAttachForwardToEventManager()
    {
        $instance = new Loop([], $this->events);
        $this->events
            ->expects($this->once())
            ->method('attach')
            ->with($name = 'test', $callback = 'is_array', $priority = 12)
        ;

        $this->assertSame(
            $instance,
            $instance->attach($name, $callback, $priority)
        );
    }
}
