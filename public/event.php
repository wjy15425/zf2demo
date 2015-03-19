<?php
ini_set('display_errors', 1);
include '../vendor/autoload.php';

//EventManager组件 1. subject/observer(观察者模式) 2. Aspect-Oriented (面向方面) 3. event-driven (事件驱动)

use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventManagerAwareInterface;

class Foo implements EventManagerAwareInterface
{
    protected $events;
    
    public function setEventManager(EventManagerInterface $events)
    {
        $events->setIdentifiers(array(
                __CLASS__,
                get_called_class(),
        ));
        $this->events = $events;
        return $this;
    }
    
    public function getEventManager()
    {
        if(null === $this->events) {
            $this->setEventManager(new EventManager());
        }
        return $this->events;
    }
    
    public function bar($baz, $bat = null)
    {
        $params = compact('baz','bat');
        $this->getEventManager()->trigger(__FUNCTION__, $this, $params);
    }
    
    public function bar2($baz, $bat = null)
    {
        $params = compact('baz','bat');
        $fun = __FUNCTION__;
        array_walk($params, function (&$item, $key) use($fun) { $item .= $fun;});
        $this->getEventManager()->trigger(__FUNCTION__, $this, $params);
    }
    
}

use Zend\Log\Logger;
$logger = new Logger();
// $writer = new Zend\Log\Writer\Stream('event.php.log');
$writer = new Zend\Log\Writer\Stream('php://output');
$logger->addWriter($writer);
$foo = new Foo();
$foo->getEventManager()->attach(array('bar', 'bar2'), function ($e) use($logger) {
    $event  = $e->getName();
    $target = get_class($e->getTarget());
    $params = json_encode($e->getParams());

    $logger->info(sprintf(
            '%s called on %s, using params %s',
            $event,
            $target,
            $params
    ));
});

$foo->bar('baz.', 'bat...');
$foo->bar2('baz2.', 'bat2...');

use Zend\EventManager\SharedEventManager;
use Zend\EventManager\StaticEventManager;
//尚未实例化组成EventManager的类，却想指定监听者，用到SharedEventManager
// $event = new SharedEventManager();
$event = StaticEventManager::getInstance();
$event->attach('Foo', 'bar', function ($e) use($logger) {
    $logger->info(sprintf(
            '%s called on %s, using params %s',
            $e->getName(),
            get_class($e->getTarget()),
            json_encode($e->getParams())
    ));
});

$foo = new Foo();
$foo->getEventManager()->setSharedManager($event);
$foo->bar('baz.', 'bat..');


