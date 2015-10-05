<?php
namespace Rubicon\Loop\Listener;

use Rubicon\Loop\LoopEvent;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;

class LoopIntervalListener extends AbstractListenerAggregate
{
    /**
     * @param EventManagerInterface $events
     */
    public function attach(EventManagerInterface $events)
    {
        $events->attach(LoopEvent::EVENT_EXECUTE_POST,  [$this, 'sleep'], Priority::NORMAL);
        $events->attach(LoopEvent::EVENT_EXECUTE_ERROR, [$this, 'sleep'], Priority::NORMAL);
    }

    /**
     * @param LoopEvent $event
     */
    public function sleep(LoopEvent $event)
    {
        $interval = $event->getConfig()->getInterval();
        if (null !== $interval) {
            usleep($interval);
        }
    }
}