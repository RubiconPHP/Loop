<?php
namespace Rubicon\Loop\Listener;

use Zend\EventManager\AbstractListenerAggregate;
use Rubicon\Loop\Exception\LoopException;
use Rubicon\Loop\LoopEvent;
use Zend\EventManager\EventManagerInterface;

class LoopRepeatListener extends AbstractListenerAggregate
{
    /**
     * @var int
     */
    private $executions;

    /**
     * @param EventManagerInterface $events
     */
    public function attach(EventManagerInterface $events)
    {
        $events->attach(LoopEvent::EVENT_LOOP_START,    [$this, 'clearExecutions'], Priority::NORMAL);
        $events->attach(LoopEvent::EVENT_EXECUTE_POST,  [$this, 'checkExecutions'], Priority::VERY_LOW);
        $events->attach(LoopEvent::EVENT_EXECUTE_ERROR, [$this, 'checkExecutions'], Priority::VERY_LOW);
    }

    public function clearExecutions()
    {
        $this->executions = 0;
    }

    /**
     * @param LoopEvent $event
     */
    public function checkExecutions(LoopEvent $event)
    {
        $repeat = $event->getConfig()->getRepeat();
        $this->executions += 1;
        if (null !== $repeat && $repeat <= $this->executions) {
            throw new LoopException('maximum loop executions reached', 0, $event->getException());
        }
    }
}