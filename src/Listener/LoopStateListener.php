<?php
namespace Rubicon\Loop\Listener;

use Zend\EventManager\AbstractListenerAggregate;
use Rubicon\Loop\Exception\LoopException;
use Rubicon\Loop\LoopEvent;
use Zend\EventManager\EventManagerInterface;

class LoopStateListener extends AbstractListenerAggregate
{
    /**
     * @param EventManagerInterface $events
     */
    public function attach(EventManagerInterface $events)
    {
        $events->attach(LoopEvent::EVENT_EXECUTE_PRE, [$this, 'checkStatus'], Priority::VERY_LOW);
    }

    /**
     * @param LoopEvent $event
     */
    public function checkStatus(LoopEvent $event)
    {
        if ($event->getTarget()->isStopped()) {
            throw new LoopException('Loop has been stopped', 0, $event->getException());
        }
    }
}