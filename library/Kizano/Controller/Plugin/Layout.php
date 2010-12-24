<?php

class Kizano_Controller_Plugin_Layout extends Zend_Controller_Plugin_Abstract{

	# Pre-dispatch marker to prevent unnecessary iteration
	protected static $_preDispatch;
	/**
	 * @var Zend_Controller_Request_Abstract
	 */
	protected $_request;

	/**
	 * @var Zend_Controller_Response_Abstract
	 */
	protected $_response;

	/**
	 * Set request object
	 *
	 * @param Zend_Controller_Request_Abstract $request
	 * @return Zend_Controller_Plugin_Abstract
	 */
	public function setRequest(Zend_Controller_Request_Abstract $request){return parent::setRequest($request);}

	/**
	 * Get request object
	 *
	 * @return Zend_Controller_Request_Abstract $request
	 */
	public function getRequest(){return $this->_request;}

	/**
	 * Set response object
	 *
	 * @param Zend_Controller_Response_Abstract $response
	 * @return Zend_Controller_Plugin_Abstract
	 */
	public function setResponse(Zend_Controller_Response_Abstract $response){return parent::setResponse($response);}

	/**
	 * Get response object
	 *
	 * @return Zend_Controller_Response_Abstract $response
	 */
	public function getResponse(){return $this->_response;}

	/**
	 * Called before Zend_Controller_Front begins evaluating the
	 * request against its routes.
	 *
	 * @param Zend_Controller_Request_Abstract $request
	 * @return void
	 */
	public function routeStartup(Zend_Controller_Request_Abstract $request){}

	/**
	 * Called after Zend_Controller_Router exits.
	 *
	 * Called after Zend_Controller_Front exits from the router.
	 *
	 * @param  Zend_Controller_Request_Abstract $request
	 * @return void
	 */
	public function routeShutdown(Zend_Controller_Request_Abstract $request){}

	/**
	 * Called before Zend_Controller_Front enters its dispatch loop.
	 *
	 * @param  Zend_Controller_Request_Abstract $request
	 * @return void
	 */
	public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request){}

	/**
	 * Called before an action is dispatched by Zend_Controller_Dispatcher.
	 *
	 * This callback allows for proxy or filter behavior.  By altering the
	 * request and resetting its dispatched flag (via
	 * {@link Zend_Controller_Request_Abstract::setDispatched() setDispatched(false)}),
	 * the current action may be skipped.
	 *
	 * @param  Zend_Controller_Request_Abstract $request
	 * @return void
	 */
	public function preDispatch(Zend_Controller_Request_Abstract $request){
		# For some reason, Zend_Filter likes to iterate over this function. Repetitively.
		if(empty(self::$_preDispatch)) self::$_preDispatch = true; else return false;
		$view = Zend_Registry::getInstance()->get('view');
		$navigation = new Zend_Navigation(new Zend_Config_Xml(DIR_APPLICATION.'configs'.DS.'navigation.xml', 'nav'));
		$view->navigation()->menu()->setContainer($navigation);
	}

	/**
	 * Called after an action is dispatched by Zend_Controller_Dispatcher.
	 *
	 * This callback allows for proxy or filter behavior. By altering the
	 * request and resetting its dispatched flag (via
	 * {@link Zend_Controller_Request_Abstract::setDispatched() setDispatched(false)}),
	 * a new action may be specified for dispatching.
	 *
	 * @param  Zend_Controller_Request_Abstract $request
	 * @return void
	 */
	public function postDispatch(Zend_Controller_Request_Abstract $request){
	}

	/**
	 * Called before Zend_Controller_Front exits its dispatch loop.
	 *
	 * @return void
	 */
	public function dispatchLoopShutdown(){}
}

