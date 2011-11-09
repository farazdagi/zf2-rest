<?php
// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Ensure ZF is on the include path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(__DIR__ . '/../library'),
    realpath(__DIR__ . '/../library/ZendFramework/library'),
    get_include_path(),
)));

require_once 'Zend/Loader/AutoloaderFactory.php';
Zend\Loader\AutoloaderFactory::factory(array('Zend\Loader\StandardAutoloader' => array()));

$appConfig = new Zend\Config\Config(include __DIR__ . '/../configs/application.config.php');

$moduleLoader = new Zend\Loader\ModuleAutoloader($appConfig['module_paths']);
$moduleLoader->register();

$moduleManager = new Zend\Module\Manager(
    $appConfig['modules'],
    new Zend\Module\ManagerOptions($appConfig['module_manager_options'])
);

// Create application, bootstrap, and run
$bootstrap      = new Zend\Mvc\Bootstrap($moduleManager);
$application    = new Zend\Mvc\Application;

// this step is not generally needed as default request is good enough for most setups
// however since we want to accept different HTTP methods (such as PATCH) we need
// to redefine the request class used
$request = new Application\Http\PhpEnvironment\Request();
$application->setRequest($request);

$bootstrap->bootstrap($application);
echo $application->run()->getBody();
