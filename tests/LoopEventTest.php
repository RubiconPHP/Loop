<?php
namespace Rubicon\Loop;

class LoopEventTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LoopEvent
     */
    protected $instance;

    public function setUp()
    {
        $loop   = $this->getMock(LoopInterface::class);
        $config = $this->getMock(LoopConfig::class);
        $this->instance = new LoopEvent($loop, $config);
    }

    public function testConstructor()
    {
        $loop   = $this->getMock(LoopInterface::class);
        $config = $this->getMock(LoopConfig::class);
        $event = new LoopEvent($loop, $config);

        $this->assertSame($loop, $event->getTarget());
        $this->assertSame($config, $event->getConfig());
    }

    public function testResultAccessor()
    {
        $this->assertNull($this->instance->getResult());
        $this->assertSame($this->instance, $this->instance->setResult($result = 'toto'));
        $this->assertSame($result, $this->instance->getResult());
    }

    public function testExceptionAccessor()
    {
        $this->assertNull($this->instance->getException());
        $this->assertSame($this->instance, $this->instance->setException($e = new \Exception));
        $this->assertSame($e, $this->instance->getException());
    }
}
