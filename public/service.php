<?php
use Zend\ServiceManager\AbstractFactoryInterface;
ini_set('display_errors', 1);
/* include 'E:/workspace/zf2/library/Zend/Loader/AutoloaderFactory.php';
Zend\Loader\AutoloaderFactory::factory(array(
    'Zend\Loader\StandardAutoloader' => array(
        'autoregister_zf' => true,
        'namespaces' => array(),
    ),
)); */
include '../vendor/autoload.php';
        
if (!class_exists('Zend\Loader\AutoloaderFactory')) {
    throw new RuntimeException('Unable to load ZF2. Run `php composer.phar install` or define a ZF2_PATH environment variable.');
}


$serviceManager = new Zend\ServiceManager\ServiceManager();
//Service registration 对象
$serviceManager->setService('myService1', array('key' => 'value'));
$serviceManager->setService('myService2', new SplFileObject('/temp.txt'));
var_dump($serviceManager->get('myService1'));
var_dump($serviceManager->get('myService2'));

//Lazy-loaded service objects. 类名
class InvokableCls {
    public $name = 'invoke';
}
$serviceManager->setInvokableClass('invoke', 'InvokableCls');
var_dump($serviceManager->get('invoke'));

//Service factories.  PHP callback、实现Zend\ServiceManager\FactoryInterface的对象或类名
class MyFactory implements Zend\ServiceManager\FactoryInterface
{
    public $name = 'factory';
    public function createService(Zend\ServiceManager\ServiceLocatorInterface $serviceLocator)
    {
        return $this;
    }
}
// registering a factory instance
$serviceManager->setFactory('factoryService1', new MyFactory());
// registering a factory by factory class name
$serviceManager->setFactory('factoryService2', 'MyFactory');
// registering a callback as a factory
$serviceManager->setFactory('factoryService3', function () { return new stdClass(); });
var_dump($serviceManager->get('factoryService1')); // object(stdClass)
var_dump($serviceManager->get('factoryService2')); // object(stdClass)
var_dump($serviceManager->get('factoryService3')); // object(stdClass)

//Service aliasing. 别名
$serviceManager->setAlias('Alias1', 'invoke');
$serviceManager->setAlias('Alias2', 'Alias1');
var_dump($serviceManager->get('Alias1')->name);
var_dump($serviceManager->get('Alias2')->name);

//Abstract factories.  If the service manager was not able to find a service for the requested name, it will check the registered abstract factories
class MyAbstractFactory implements Zend\ServiceManager\AbstractFactoryInterface
{
    public function canCreateServiceWithName(Zend\ServiceManager\ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        // this abstract factory only knows about 'factory1' and 'factory2'
        return $requestedName === 'factory1' || $requestedName === 'factory2';
    }
    public function createServiceWithName(Zend\ServiceManager\ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $service = new stdClass();
        $service->name = $requestedName;
        return $service;
    }
}
class MyAbstractFactory2 implements Zend\ServiceManager\AbstractFactoryInterface
{
    public function canCreateServiceWithName(Zend\ServiceManager\ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        // this abstract factory only knows about 'factory1' and 'factory2'
        return $requestedName === 'factory2' || $requestedName === 'factory-3';
    }
    public function createServiceWithName(Zend\ServiceManager\ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        echo '=================<br/>$name:'.$name.' $requestedName:'.$requestedName.'<br/>=================<br/>';
        $service = new stdClass();
        $service->name = $requestedName.' by '.__CLASS__;
        return $service;
    }
}
$serviceManager->addAbstractFactory('MyAbstractFactory');
$serviceManager->addAbstractFactory('MyAbstractFactory2', false);//default true; if false unshift to abstractFactories, if ture push to abstractFactories
var_dump($serviceManager->get('factory1')->name);
echo '==========================================<br/>';
var_dump($serviceManager->get('factory2')->name);//by MyAbstractFactory2
var_dump($serviceManager->get('factory-3')->name);
//var_dump($serviceManager->get('factory4')->name);//unable to fetch or create an instance for factory4

//Initializers
class MyInitializer implements Zend\ServiceManager\InitializerInterface
{
    public function initialize($instance, Zend\ServiceManager\ServiceLocatorInterface $serviceLocator)
    {
        if($instance instanceof \stdClass) {
            $instance->initialized = 'initalized';
        } else {
            $instance->initialized = 'initalized-no';
        }
    }
}

$serviceManager->addInitializer('MyInitializer');
$serviceManager->setInvokableClass('my-initialize-service', 'stdClass');
var_dump($serviceManager->get('my-initialize-service')->initialized);


//Delegator factory
class Buzzer
{
    public function buzz()
    {
        return 'Buzz!';
    }
}
use Zend\EventManager\EventManagerInterface;

/** 委托者类 */
class BuzzerDelegator extends Buzzer
{
    protected $realBuzzer;
    protected $eventManager;
    
    public function __construct(Buzzer $realBuzzer, EventManagerInterface $eventManager)
    {
        $this->realBuzzer = $realBuzzer;
        $this->eventManager = $eventManager;
    }
    public function buzz()
    {
        $this->eventManager->trigger('buzz', $this);
        return $this->realBuzzer->buzz();
    }
}
$wrappedBuzzer = new Buzzer();
$eventManager = new Zend\EventManager\EventManager();
$eventManager->attach('buzz', function () { echo "start at the art!\n";});
$buzzer = new BuzzerDelegator($wrappedBuzzer,$eventManager);
echo '<br/><b>Delegator:</b><br/>';
echo $buzzer->buzz();

use Zend\ServiceManager\DelegatorFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
/** 委托者工厂,生产委托者类 */
class BuzzerDelegatorFactory implements DelegatorFactoryInterface
{
    public function createDelegatorWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName, $callback)
    {
        $realBuzzer = call_user_func($callback);
        $eventManager = $serviceLocator->get('EventManager');
        $eventManager->attach('buzz', function () { echo "Stare at the art!\n"; });
        return new BuzzerDelegator($realBuzzer, $eventManager);
    }
}
echo '<br/><b>DelegatorFactory:</b><br/>';
$serviceManager = new \Zend\ServiceManager\ServiceManager();
$serviceManager->setInvokableClass('EventManager', 'Zend\EventManager\EventManager');
$serviceManager->setInvokableClass('buzzer', 'Buzzer');//注册buzzer服务
$serviceManager->setInvokableClass('buzzer-delegator-factory', 'BuzzerDelegatorFactory');//委托者工厂服务
$serviceManager->addDelegator('buzzer', 'buzzer-delegator-factory');//用委托者工厂服务(buzzer-delegator-factory)处理buzzer服务
// var_dump($serviceManager->getRegisteredServices());die;
$buzzer = $serviceManager->get('buzzer');
$buzzer->buzz();
//通过配置来设置委托者工厂
$config_arr = array(
        'invokables' => array(
                'EventManager' => 'Zend\EventManager\Eventmanager',
                'buzzer'       => 'Buzzer',
                'buzzer-delegator-factory' => 'BuzzerDelegatorFactory',
        ),
        'delegators' => array(
                'buzzer' => array(
                        'buzzer-delegator-factory',
                )
         ),
);
$config = new Zend\ServiceManager\Config($config_arr);
$serviceManager = new Zend\ServiceManager\ServiceManager($config);
$serviceManager->get('buzzer')->buzz();

echo '<br/>============================================<br/>';



// Lazy Services
class MyBuzzer
{
    public function __construct()
    {
        // deliberately halting the application for 5 seconds
        sleep(1);
        echo __FUNCTION__;
    }

    public function buzz()
    {
        return 'MyBuzz!';
    }
}
$serviceManager = new Zend\ServiceManager\ServiceManager();
$serviceManager->setService('Config', array(
    'lazy_services' => array(
        'class_map' => array(
            'buzzer' => 'MyBuzzer',//延迟服务名、服务类名
        )
    ),
));
$serviceManager->setInvokableClass('buzzer', 'Mybuzzer');
$serviceManager->setFactory('LazyServiceFactory', 'Zend\ServiceManager\Proxy\LazyServiceFactoryFactory');//延迟委服务委托者工厂
$serviceManager->addDelegator('buzzer', 'LazyServiceFactory');//用延迟委服务委托者工厂处理buzzer服务
$buzzer = $serviceManager->get('buzzer');
for ($i = 0; $i < 100; $i += 1) {
    $buzzer = $serviceManager->create('buzzer');//延迟服务，不会实例化MyBuzzer(MyBuzzer#__construct)
    echo "created buzzer $i\n";
}
echo $buzzer->buzz();//真正用到buzzer服务时方执行实例化(MyBuzzer#__construct)

