<?php
require_once __DIR__ . '/../autoload_register.php';

$rootPath  = realpath(dirname(__DIR__));
$testsPath = "$rootPath/tests";

$path = array(
    $testsPath,
    realpath(__DIR__ . '/../../../library/ZendFramework/library'),
    get_include_path(),
);
set_include_path(implode(PATH_SEPARATOR, $path));

require_once 'Zend/Loader/AutoloaderFactory.php';
\Zend\Loader\AutoloaderFactory::factory(array('Zend\Loader\StandardAutoloader' => array()));

$moduleLoader = new \Zend\Loader\ModuleAutoloader(array(
    realpath(__DIR__ . '/../..'),
    realpath(__DIR__ . '/../../..')
));
$moduleLoader->register();

$moduleManager = new \Zend\Module\Manager(array('Gists'));
$moduleManager->loadModule('Gists');

//$config = $moduleManager->getMergedConfig()->toArray();

//\GistsTest\TestCase::$config = $config;
