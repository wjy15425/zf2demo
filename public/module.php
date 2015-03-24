<?php
ini_set('display_errors', 1);
include '../vendor/autoload.php';

// Zend\ModuleManager\ModuleManager 负责遍历模块名称数组，并触发一系列事件。
// module类实例化、初始化任务和配置都由附加的事件监听器执行
// Zend\ModuleManager\ModuleManager
// loadModules (ModuleEvent::EVENT_LOAD_MODULES) 事件主要用来在事件监听器内部帮助封装模块加载工作
// loadModule.resolve (ModuleEvent::EVENT_LOAD_MODULE_RESOLVE) 