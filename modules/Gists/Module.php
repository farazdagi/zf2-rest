<?php

namespace Gists;

use Zend\Module\Manager,
    Zend\Config\Config,
    Zend\EventManager\StaticEventManager,
    Zend\Loader\AutoloaderFactory;

class Module
{
    public function init(Manager $moduleManager)
    {
        $this->initAutoloader($moduleManager->getOptions()->getApplicationEnv());
    }

    public function initAutoloader()
    {
        require __DIR__ . '/autoload_register.php';
    }

    public function getConfig()
    {
        return new Config(include __DIR__ . '/configs/module.config.php');
    }
}
