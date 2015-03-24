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
        //标识符
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
        $this->getEventManager()->trigger(__FUNCTION__, $this, $params, function ($v) {
            return $v;
        });
    }
    
    public function otherFun($a, $b)
    {
        $this->getEventManager()->trigger(__FUNCTION__, $this, func_get_args());
    }
    
    public function otherFun2($a, $b, $c)
    {
        $this->getEventManager()->trigger(__FUNCTION__, $this, func_get_args());
    }
    
    public function inject(array $values)
    {
        $argv = compact('values');
        $argv = $this->getEventManager()->prepareArgs($argv);
        $date = isset($argv['values']['date']) ? $argv['values']['date'] : new DateTime();
        $result = $this->getEventManager()->trigger(__FUNCTION__, $this, $argv);
        if($result->stopped()) {
            $result->last();//检索最后执行的监听者的返回值(Zend\EventManager\ResponseCollection) 
        }
    }
}

use Zend\Log\Logger;
$logger = new Logger();
// $writer = new Zend\Log\Writer\Stream('event.php.log');
$writer = new Zend\Log\Writer\Stream('php://output');
$logger->addWriter($writer);
$foo = new Foo();
//attach 多个事件可用数组  attach(事件, 监听者[, 优先级])
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
}, 1);
// * 通配符 attach所有事件
$foo->getEventManager()->attach('*', function ($e) use ($logger) {
    $logger->info(sprintf(
            'attach * : %s called on %s, using params:%s',
            $e->getName(),
            get_class($e->getTarget()),
            json_encode($e->getParams())
    ));
    $e->stopPropagation(true);
    return $e->getParams();
}, 2);

$foo->getEventManager()->attach('inject', function ($e) use ($logger) {
    $values = $e->getParam('values');
    if(!$values) return;
    if(!isset($values['date'])) {
        $values['date'] = new DateTime();
        return;
    }
    $values['date'] = new \DateTime($values['date']);
    $logger->info(sprintf(
            'attach inject : %s called on %s, using params:%s',
            $e->getName(),
            get_class($e->getTarget()),
            json_encode($e->getParams())
    ));
    //$e->stopPropagation(true);//在这之后inject的监听者无效
    return $values;
}, 3);
$foo->inject(array(
        'date' => '2015-03-20'
));
$foo->bar('baz.', 'bat...');
$foo->bar2('baz2.', 'bat2...');
$foo->otherFun('a', 'b');
$foo->otherFun2('a', 'b', 'c');

use Zend\EventManager\SharedEventManager;
use Zend\EventManager\StaticEventManager;
//尚未实例化组成EventManager的类，却想指定监听者，用到SharedEventManager
// $event = new SharedEventManager();
$event = StaticEventManager::getInstance();
//如需要可第1个和第二个参数可为数组，实现attach多个事件到多个观察者对象
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
// $foo->bar('baz.', 'bat..');

//ListenerAggregateInterface实现缓存
class SomeValueObject implements EventManagerAwareInterface
{
    protected $events;
    
    public function get($id)
    {
        $params = compact('id');
        $result = $this->getEventManager()->trigger('get.pre', $this, $params);
        if($result->stopped()) {
            return $result->last();
        }
        $content = sprintf('%s this is computed content $id:%s', date('Y-m-d H:i:s'), $id);
        $params['__RESULT__'] = $content;
        $this->getEventManager()->trigger('get.post', $this, $params);
        return $content;
    }
    
    public function setEventManager(EventManagerInterface $events)
    {
        $events->setIdentifiers(array(
                __CLASS__,
                get_called_class()
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
}
use Zend\EventManager\ListenerAggregateInterface;

//use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventInterface;
use Zend\Cache\Storage\StorageInterface;
class CacheListener implements ListenerAggregateInterface
{
    protected $cache;
    protected $listeners = array();
    
    public function __construct(StorageInterface $cache)
    {
        $this->cache = $cache;
    }
    /** Attach one or more listeners */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach('get.pre', array($this, 'load'), 100);
        $this->listeners[] = $events->attach('get.post', array($this, 'save'), -100);
    }
    
    /** Detach all previously attached listeners */
    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }
    /** 获取缓存数据 */
    public function load(EventInterface $e)
    {
        $params = $e->getParams();
        $id = get_class($e->getTarget()).'-'.$params['id'];
        $content = $this->cache->getItem($id);
        if(false !== $content && null !== $content) {
            $e->stopPropagation();
            return $content;
        }
    }
    /** 缓存数据 */
    public function save(EventInterface $e) {
        $params = $e->getParams();
        $content = $params['__RESULT__'];
        unset($params['__RESULT__']);
        $params = $e->getParams();
        $id = get_class($e->getTarget()).'-'.$params['id'];
        $this->cache->setItem($id, $content);
    }
}

$value = new SomeValueObject();
use Zend\Cache\StorageFactory;
$cache = StorageFactory::factory(array(
        'adapter' => array(
                'name' => 'Filesystem',
                'options' => array(
                        'cache_dir' => '..'.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'cache',
                ),
        ),
        'plugins' => array(
                'serializer',
        ),
));
$cacheListener = new CacheListener($cache);
$value->getEventManager()->attach($cacheListener);
var_dump($value->get('001'));

