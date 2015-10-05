<?php
namespace Rubicon\Loop\Listener;

use Rubicon\Loop\LoopConfig;
use Rubicon\Loop\LoopEvent;
use Rubicon\Loop\LoopInterface;
use Zend\EventManager\EventManagerInterface;

class LoopIntervalListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $events;

    /**
     * @var LoopIntervalListener
     */
    private $instance;

    public function setUp()
    {
        $this->instance = new LoopIntervalListener();
        $this->events   = $this->getMock(EventManagerInterface::class);
    }

    public function testAttach()
    {
        $this->events
            ->expects($this->exactly(2))
            ->method('attach')
            ->withConsecutive(
                [LoopEvent::EVENT_EXECUTE_POST, [$this->instance, 'sleep'], Priority::NORMAL],
                [LoopEvent::EVENT_EXECUTE_ERROR, [$this->instance, 'sleep'], Priority::NORMAL]
            )
        ;

        $this->instance->attach($this->events);
    }

    public function testSleep()
    {
        $config = new LoopConfig();
        $event  = new LoopEvent($this->getMock(LoopInterface::class), $config);

        $time = microtime(true);
        $this->instance->sleep($event);
        $this->assertGreaterThanOrEqual(microtime(true), $time + 0.01);

        $config->setInterval(100);
        $time = microtime(true);
        $this->instance->sleep($event);
        $this->assertGreaterThanOrEqual(microtime(true), $time + 0.15);
    }
}
