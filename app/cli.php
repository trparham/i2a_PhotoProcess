<?php

use \Phalcon\Cli\Console as ConsoleApp;

define('VERSION', '1.0.0');

// Using the CLI factory default services container
$di = new Phalcon\Di\FactoryDefault\Cli();

// Define path to application directory
defined('APPLICATION_PATH')
|| define('APPLICATION_PATH', realpath(dirname(__FILE__)));

/**
 * Register the autoloader and tell it to register the tasks directory
 */
$loader = new \Phalcon\Loader();
$loader->registerDirs(
    array(
        APPLICATION_PATH . '/tasks',
        APPLICATION_PATH . '/controllers',
        APPLICATION_PATH . '/models',
        APPLICATION_PATH . '/service',
        APPLICATION_PATH . '/library'
    )
);
$loader->register();

$config = new Phalcon\Config\Adapter\Json(APPLICATION_PATH . '/config/config.json');
$di->set('config', $config);

// Create a console application
$console = new ConsoleApp();
$console->setDI($di);

/**
 * Provide database
 */
$di->set('db', function() use ($config) {
    return new \Phalcon\Db\Adapter\Pdo\Postgresql(array(
        "host" => $config->database->host,
        "username" => $config->database->username,
        "password" => $config->database->password,
        "dbname" => $config->database->name,
        "schema" => 'public'
    ));
});

/**
 * Setup Mail service
 */
$di->set('mail', function(){
        return new Mail();
});

/**
 * Setup view
 */
$di->set('view', function() {
    $view = new \Phalcon\Mvc\View();
    $view->setViewsDir(APPLICATION_PATH . '/views/');
    return $view;
});

/**
 * Process the console arguments
 */
$arguments = array();
foreach ($argv as $k => $arg) {
    if ($k == 1) {
        $arguments['task'] = $arg;
    } elseif ($k == 2) {
        $arguments['action'] = $arg;
    } elseif ($k >= 3) {
        $arguments['params'][] = $arg;
    }
}

// Define global constants for the current task and action
define('CURRENT_TASK',   (isset($argv[1]) ? $argv[1] : null));
define('CURRENT_ACTION', (isset($argv[2]) ? $argv[2] : null));

try {
    // Handle incoming arguments
    $console->handle($arguments);
} catch (\Phalcon\Exception $e) {
    echo $e->getMessage();
    exit(255);
}