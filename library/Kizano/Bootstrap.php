<?php

/**
 *	Application's Bootstrap based on the Zend Appliction Bootstrap. Bootstraps the application.
 *	@property	view					Kizano_View				Public		The view for each module
 *	@property	_config					Zend_Config				Protected	The central configuration for this class
 *	@method		_bootstrap				void					Protected	Overrides the parent bootstrap to only execute
 *																			the resources and not the plugin as well.
 *	@method		run						Array					Public		Executes the dispatcher for this application
 *	@method		_initConfigs			Array					Protected	Returns the configuration for this application
 *	@method		_setResource			void					Protected	Shortcut function to $this->_options['resources']
 *	@method		getResource				Array					Public		Gets a resource from $this->_options['resources']
 *	@method		_initAutoLoader			Zend_Loader_AutoLoader	Protected	Inits the AutoLoader for namespaces
 *	@method		_initFrontController	Zend_Controller_Front	Protected	Inits the Front-end Controller
 *	@method		_initModules			Array					Protected	Inits the Module names into the AutoLoader
 *	@method		_initCache				Zend_Cache				Protected	Inits Zend's ability to cache
 *	@method		_initSession			Zend_Session_Namespace	Protected	Inits the Session for this user
 *	@method		_initLayout				Zend_Layout				Protected	Inits the Zend_Layout
 *	@method		_initView				Kizano_View				Protected	Inits the View for this app
 */
class Kizano_Bootstrap extends Zend_Application_Bootstrap_Bootstrap{

	# This instance of a Zend_View or a derived class of it
	public $view;

	/**
	 *	Execute this application!
	 *	@return void
	 */
	public function run(){
		$this->bootstrap('frontController');
		$this->_frontController->dispatch();
	}

	/**
	 *	Overrides the default bootstrapping function to prevent autoloading plugins
	 *	if they don't exist.
	 *	@return void
	 */
	protected function _bootstrap($resource = null){
		if(is_null($resource)){
			foreach($this->_options['resources'] as $name => $resource){
				$this->_setResource($name, $resource);
				$this->_executeResource($name);
			}
        }elseif (is_string($resource)){
            $this->_executeResource($resource);
        }elseif (is_array($resource)){
            foreach ($resource as $r){
                $this->_executeResource($r);
            }
        }else{
            throw new Zend_Application_Bootstrap_Exception(sprintf('%s::%s(): Invalid argument passed!', __CLASS__, __METHOD__));
        }
	}

	/**
	 *	Initializes the configuration
	 *	@return		Array		The configuration loaded.
	 */
	protected function _initConfigs(){
		Zend_Registry::getInstance()->isRegistered('config') || Zend_Registry::getInstance()->set('config', $this->_options);
		return $this->_options;
	}

	/**
	 *	Internal function to set a given resource given by the resource object in the
	 *	application.ini configuration file.
	 *	@return void
	 */
	protected function _setResource($name, $resource = null){
		$this->_options['resources'][$name] = $resource;
	}

	/**
	 *	Gets a resource from the configuration.
	 *	@param	name	String		The name of the config index to retrieve
	 *	@return			Object		The configuration at that point of index.
	 */
	public function getConfig($name){
		return isset($this->_options['resources'][$name])? (object)$this->_options['resources'][$name]: null;
	}

	/**
	 *	Starts up the autoloader.
	 *	@return Zend_Loader_Autoloader
	 */
	protected function _initAutoLoader(){
		require_once 'Doctrine.php';
		spl_autoload_register(array('Doctrine', 'autoload'));
		$autoLoader = $this->getApplication()->getAutoLoader();
		$this->_environment = $_SERVER['ENVIRONMENT'];
		$this->bootstrap('configs');
		$this->registerPluginResource('autoloader', $autoLoader);
#		var_dump($autoLoader);die;
		return $autoLoader;
	}

	/**
	 *	Initializes the front controller.
	 *	@return Zend_Controller_Front
	 */
	protected function _initFrontController(){
		$this->bootstrap('configs');
		$this->bootstrap('layout');
		$this->_frontController = Zend_Controller_Front::getInstance();
		$this->_frontController->addModuleDirectory(DIR_APPLICATION.'modules');
		$this->_frontController->registerPlugin(new Kizano_Controller_Plugin_Layout);
		$this->_frontController->registerPlugin(new Kizano_View_Plugins_Layout);
		$this->_frontController->unRegisterPlugin('Zend_Layout_Controller_Plugin_Layout'); # <- Get rid of this annoying-ass class! >:|
		return $this->_frontController;
	}

	/**
	 *	Initializes the modules and registers their namespaces to ensure easy loading.
	 *	@return void
	 */
	protected function _initModules(){
		$this->bootstrap('autoloader');
		require 'Kizano/Application/Resource/ModuleConfig.php';
		$resource = 'ModuleConfig';
		$this->registerPluginResource($resource)
			 ->_executeResource($resource);
		$modules = (array)$this->getConfig('modules');
		$loader = $this->getApplication()->getAutoLoader();
		$loader->registerNamespace($modules);
		foreach(array_keys($modules) as $module){
			$controllers[$module] = DIR_APPLICATION.'modules'.DS.$module.DS.'controllers';
		}
		$this->_frontController->setControllerDirectory($controllers);
	}

	/**
	 * Initialize the cache
	 *
	 * @return Zend_Cache_Core
	 */
	protected function _initCache(){
		if(!LIVE) return false;
		$cache = Zend_Cache::factory('Core', 'File', $this->getConfig('cache')->frontendOptions);
		Zend_Registry::set('cache', $cache);
		return $cache;
	}

	/**
	 *	Initializes the current session and assigns it to the registry
	 *	@return Zend_Session
	 */
	protected function _initSession(){
		$sess = $this->getConfig('session');
		$session = new Zend_Session_Namespace($sess->name, true);
		Zend_Registry::getInstance()->set('session', $session);
		return $this->getConfig('session');
	}

	/**
	 *	Starts up the database manager.
	 *	@return Doctrine_Core
	 */
	protected function _initDB(){
		$this->bootstrap('AutoLoader');
		$config = $this->getConfig('db')->params;
		$dsn = sprintf(
			'mysql://%s:%s@%s/%s',
			$config['username'],
			$config['password'],
			$config['host'],
			$config['dbname']
		);
		$DB = Doctrine_Manager::connection($dsn);
		Zend_Registry::getInstance()->set('db', $DB);
		return $DB;
	}

	/**
	 *	Initializes the layout controller
	 *	@return Zend_Layout
	 */
	protected function _initLayout(){
		$layout = (array)$this->getConfig('layout');
		$this->_layout = Zend_Layout::startMVC($layout);
		Zend_Registry::getInstance()->set('layout', $this->_layout);
		return $this->_layout;
	}

	/**
	 *	Initializes the View controller
	 *	@return Zend_View
	 */
	protected function _initView(){
		$this->bootstrap('autoloader');
		$this->bootstrap('layout');
		$this->view = $this->_layout->getView();
		$this->view
			->addHelperPath('Kizano/View/Helper/', 'Kizano_View_Helper')
			->doctype('XHTML1_STRICT')
		;
		$render = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
		$render->setView($this->view)
			->setViewScriptPathSpec(':controller/:action.:suffix')
			->setViewScriptPathNoControllerSpec(':action.:suffix');
		$this->_layout->setView($this->view);
		Zend_Registry::getInstance()->set('view', $this->view);
		return $this->view;
	}
}

