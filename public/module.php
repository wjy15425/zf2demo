<?php
ini_set('display_errors', 1);
include '../vendor/autoload.php';

/**
 * MVC
 * MVC ModuleManagerFactory attach DefaultListenerAggregate
 * DefaultListenerAggregate attach LocatorRegistrationListener
 * LocatorRegistrationListener attach ModuleEvent::EVENT_LOAD_MODULE ModuleEvent::EVENT_LOAD_MODULES
 */

// Zend\ModuleManager\ModuleManager 负责遍历模块名称数组，并触发一系列事件。
// module类实例化、初始化任务和配置都由附加的事件监听器执行
// Zend\ModuleManager\ModuleManager

// loadModules (ModuleEvent::EVENT_LOAD_MODULES) 
// 事件主要用来帮助在事件监听器内部封装 模块加载工作 

// loadModule.resolve (ModuleEvent::EVENT_LOAD_MODULE_RESOLVE) 
// 为每个将要加载的module触发,此监听器负责获取module名称并解析为某个类实例
// ZF2提供的默认的module解析器，只需查找{modulename}\Module类 ,如果存在实例化并返回。


// Zend\ModuleManager\Listener\DefaultListenerAggregate
// ZF2提供这个默认的aggregate listener，用来简化module manager最常见的use case。
// 大部分情况下，这是module manager唯一需要绑定(attach)的listener,它负责正确地绑定必需的listeners(下面列出)以保障模块系统正常运行
// 1. Zend\ModuleManager\Listener\AutoloaderListener 检查每个module是否实现了 Zend\ModuleManager\Feature\AutoloaderProviderInterface 接口
//    或 定义了getAutoloaderConfig()方法，如果是则调用此方法并传递返回的数组给 Zend\Loader\AutoloaderFactory
// 2. Zend\ModuleManager\Listener\ModuleDependencyCheckerListener 检查module的所有依赖是否已加载。 getModuleDependencies
// 3. Zend\ModuleManager\Listener\ConfigListener module配置合并到主应用配置 getConfig
// 4. Zend\ModuleManager\Listener\InitTrigger zend\ModuleManager\ModuleManager作为基本参数 init($modelManager)
//    与OnBootstrapListener相似，每个页面请求都调用到，只能用于执行轻量级任务，例如注册事件侦听器
