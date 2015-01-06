<?php
namespace Checklist;

use Checklist\Model\TaskMapper;
use Zend\Mvc\Controller\PluginManager;

class Module 
{
    //will be triggered by a Zend\ModuleManager listener when it loads the module class
    public function init($manager)
    {
       //ligtweight task such as registering event listeners
    }
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
    
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
    
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'TaskMapper' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $mapper = new TaskMapper($dbAdapter);
                    return $mapper;
                },
            ),
            'abstract_factories' => array(
                
            ),
            'delegators' => array(
                
            ),
        );
    }
}