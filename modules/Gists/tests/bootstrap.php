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

$moduleManager = new \Zend\Module\Manager(array('SpiffyDoctrine', 'Application', 'Gists'));
$moduleManager->loadModules();

$mergedConfig = $moduleManager->getMergedConfig()->toArray();

// you can setup sqlite database for testing
// right now I use single mysql database for tests
/*
$config = &$mergedConfig['di']['instance']['doctrine']['parameters'];
$config['conn'] = array(
    'driver' => 'pdo_sqlite',
    'path'   => __DIR__ . '/test-db'
);
//*/

// test case in turn setups (and exposes) service locator, entity manager etc
\GistsTest\Framework\TestCase::$config = $mergedConfig;
unset($config, $mergedConfig, $moduleManager, $path, $testsPath, $rootPath);
