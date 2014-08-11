<?php

namespace CheckList\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;

use Zend\I18n\Translator\TranslatorServiceFactory;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

use Zend\I18n\Translator\Translator;
use Zend\Mvc\Controller\Plugin\PluginInterface;
use Zend\Stdlib\DispatchableInterface as Dispatchable;


class Translate implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $serviceLocator = $serviceLocator->getController()->getServiceLocator();
        $serviceFactory = new TranslatorServiceFactory();
        $translator = $serviceFactory->createService($serviceLocator);

        return new TranslatorProxy($translator);
    }
}

final class TranslatorProxy extends AbstractPlugin
{
    private $translator;

    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    public function __invoke($message, $textDomain = 'default', $locale = null)
    {
        return $this->translator->translate($message, $textDomain, $locale);
    }

    public function __call($method, $args)
    {
        return call_user_func_array(array($this->translator, $method), $args);
    }

    public static function __callstatic($method, $args)
    {
        return call_user_func_array(array($this->translator, $method), $args);
    }
}