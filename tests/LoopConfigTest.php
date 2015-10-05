<?php
namespace Rubicon\Loop;

use Rubicon\Loop\Exception\LogicException;
use Zend\EventManager\ListenerAggregateInterface;

class LoopConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LoopConfig
     */
    protected $instance;

    protected function setUp()
    {
        $this->instance = new LoopConfig();
    }

    public function testConstructor()
    {
        $instance = new LoopConfig([
            'interval' => 10,
            'repeat'   => 11
        ]);

        $this->assertSame(10000, $instance->getInterval());
        $this->assertSame(11, $instance->getRepeat());
    }

    public function testIntervalAccessor()
    {
        $this->assertNull($this->instance->getInterval());
        $this->assertSame($this->instance, $this->instance->setInterval('10'));
        $this->assertSame(10000, $this->instance->getInterval());

        $this->setExpectedException(LogicException::class);
        $this->instance->setInterval('not a valid number');
    }

    public function testRepeatAccessor()
    {
        $this->assertNull($this->instance->getRepeat());
        $this->assertSame($this->instance, $this->instance->setRepeat('10'));
        $this->assertSame(10, $this->instance->getRepeat());

        $this->setExpectedException(LogicException::class);
        $this->instance->setRepeat(-1);
    }

    public function testCallbackAccessor()
    {
        $this->assertNull($this->instance->getCallback());
        $this->assertSame($this->instance, $this->instance->setCallback('trim'));
        $this->assertSame('trim', $this->instance->getCallback());
    }

    public function testListenerAccessor()
    {
        $defaults = [
            Listener\LoopStateListener::class,
        ];
        $this->assertSame($defaults, $this->instance->getListeners());
        $this->assertSame(
            $this->instance,
            $this->instance->addListener($listener = $this->getMock(ListenerAggregateInterface::class))
        );
        $this->assertContains($listener, $this->instance->getListeners());

        $this->instance->addListener($listener = get_class($this->getMock(ListenerAggregateInterface::class)));
        $this->assertContains($listener, $this->instance->getListeners());
    }

    public function testSetListeners()
    {
        $this->assertSame(
            $this->instance,
            $this->instance->setListeners([$listener = $this->getMock(ListenerAggregateInterface::class)])
        );
        $this->assertContains($listener, $this->instance->getListeners());
    }

    /**
     * @dataProvider listenerErrorDataProvider
     */
    public function testListenerAccessorErrors($listener)
    {
        $this->setExpectedException(LogicException::class);
        $this->instance->addListener($listener);

    }

    public function listenerErrorDataProvider()
    {
        return [
            [new \ArrayObject],
            ['ArrayObject'],
        ];
    }
}

