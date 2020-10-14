<?php
try {
	
	/**
	 * Read configuration file
	 */
	$config = new Phalcon\Config\Adapter\Json(__DIR__ . '/../app/config/config.json');
	
	define('APPLICATION_PATH', '../app');

	/**
	 * Create dependency injector
	 */
	$di = new Phalcon\DI\FactoryDefault();
	
	/**
	 * Store configuration
	 */
	$di['config'] = $config;
	
	/**
	 * Setup server timezone, should match the timezone set on the server.
	 */
	date_default_timezone_set($config->constants->timezone);
	
	/**
	 * Create auto loader and register directories
	 */
	$loader = new \Phalcon\Loader();
	
	$loader->registerDirs(
			array(
					__DIR__ . $config->application->controllersDir,
					__DIR__ . $config->application->pluginsDir,
					__DIR__ . $config->application->modelsDir,
					__DIR__ . $config->application->serviceDir,
					__DIR__ . $config->application->libraryDir,
			)
	)->register();
	
	/**
	 * Setup view
	 */
	$di->set('view', function(){
		$view = new \Phalcon\Mvc\View();
		$view->setViewsDir('../app/views/');
		return $view;
	});
    
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
	 * Start session when requested
	 * header required for iframes for some browsers
	 */
	$di->set('session', function(){
		$session = new Phalcon\Session\Adapter\Files();
		$session->start();
		return $session;
	});
    
	/**
	 * We register the events manager
	 */
	$di->set('dispatcher', function() use ($di) {
	
		$eventsManager = $di->getShared('eventsManager');
		
		// @TODO: Enable Security when needed
		//$security = new Security($di);
		
		/**
		 * We listen for events in the dispatcher using the Security plugin
		*/
		// @TODO: Enable Security when needed
		//$eventsManager->attach('dispatch', $security);
		
		$dispatcher = new Phalcon\Mvc\Dispatcher();
		$dispatcher->setEventsManager($eventsManager);
		
		return $dispatcher;
	});
		
	/**
	 * Register the SESSION flash service with custom CSS classes
	 * Changed classes to use bootstrap classes
	*/	
	$di->set('flashSession', function(){
		return new Phalcon\Flash\Session(array(
				'error' => 'alert alert-danger',
				'warning' => 'alert alert-warning',
				'success' => 'alert alert-success',
				'notice' => 'alert alert-info',
		));
	});
	
	/**
	 * Setup error handler
	 */
	define('DISPLAY_ERRORS', true);
	define('SEND_ERRORS', false); // XXX: depends on mail server setup

	if (DISPLAY_ERRORS === true) {
		$debug = new \Phalcon\Debug();
		$debug->listen();
	}
    	
	/**
	 * Handle requests
	 */
	$application = new \Phalcon\Mvc\Application($di);
	
	echo $application->handle()->getContent();

} catch (Exception $e){
	echo $e->getMessage();
}
