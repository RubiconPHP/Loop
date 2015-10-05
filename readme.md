### Event Supported Loop

Usage Example:

```
namespace Rubicon\Loop;

(new Loop([
        'interval' => 100,
        'repeat'   => 26,
    ]))
    ->attach(LoopEvent::EVENT_LOOP_START, function() {
        echo 'start...' . PHP_EOL;
    })
    ->attach(LoopEvent::EVENT_EXECUTE_POST, function(LoopEvent $event) {
        echo $event->getResult() . PHP_EOL;
    })
    ->attach(LoopEvent::EVENT_LOOP_STOP, function(LoopEvent $event) {
        echo $event->getException()->getMessage() . PHP_EOL;
    })
    ->invoke(function(LoopEvent $event) {
        $result = $event->getResult();
        return $result ? ++$result : 'A';
    })
;
```