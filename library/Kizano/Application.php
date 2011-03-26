<?php

require 'Zend/Loader/Autoloader.php';
class Kizano_Application{

	public $view;
	protected $_config;
	protected $_request;

	public function __construct($options = array()){
		$_ENV['app'] =& $this;
		Zend_Loader_AutoLoader::getInstance()
			->registerNamespace('Skillet')
			->registerNamespace('Smarty');
		if($options instanceof Zend_Config){
			$this->_config = new Zend_Config(array(), true);
			$this->_config->merge($options);
		}elseif(is_array($options) || is_string($options)){
			$this->_config = new Zend_Config($options, true);
		}else{
			require 'Skillet/Exception.php';
			throw new Skillet_Exception(sprintf("%s::%s(): \$options of type %s !expected. Expecting Zend_Config, array or string.", __CLASS__, __FUNCTION__, getType($options)));
		}
		$this->_request = new Zend_Controller_Request_Http();
		$registry = Zend_Registry::getInstance();
		$registry->set('config', $this->_config);
		$registry->set('application', $this);
		$registry->set('request', $this->_request);
		$this->bootstrap();
		return $this;
	}

	public function __get($name){return isset($this->_config->$name)? $this->_config->$name: null;}
	public function __set($name, $val = null){return $this->_config->$name = $val;}

	public function bootstrap(){
		Zend_Registry::getInstance()->isRegistered('bootstrap') || Zend_Registry::getInstance()->set('bootstrap', new Skillet_Bootstrap());
		return Zend_Registry::getInstance()->get('bootstrap');
	}

	public function run(){
		$this->view = Zend_Registry::getInstance()->get('view');
		$this->view->placeholder('page')->set('default/index.tpl');
		$this->bootstrap()->run();
		switch($this->module){
			case 'default': default:
				switch($this->controller){
					case 'index': case 'user': default:
						switch($this->action){
							case 'contact':
								$this->view->placeholder('page')->set('default/contact.tpl');
								$this->view->placeholder('navigation')->set('default/navigation.tpl');
								$this->view->placeholder('footer')->set('default/footer.tpl');
								$this->view->navigation = array('Home'=>'/', 'About'=>'/default/index/about', 'Contact'=>'/default/index/contact');
							break;
							case 'index': case 'home': default:
								$this->view->placeholder('page')->set('default/index.tpl');
								$this->view->placeholder('navigation')->set('default/navigation.tpl');
								$this->view->placeholder('footer')->set('default/footer.tpl');
								$this->view->navigation = array('Home'=>'/', 'About'=>'/default/index/about', 'Contact'=>'/default/index/contact');
						}
				}
		}
		$this->view->render(realpath(DIR_INCLUDES.DS.'layouts'.DS.'default'.DS.'master.tpl'));
		return $this;
	}
}

