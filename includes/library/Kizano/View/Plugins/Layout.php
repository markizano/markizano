<?php

class Kizano_View_Plugins_Layout extends Zend_Controller_Plugin_Abstract{
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
		$view->setScriptPath(DIR_APPLICATION.$request->getModuleName().DS.'views'.DS.'scripts'.DS);
#		var_dump($view);die;
		$render = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
		$render->setView($view)
			->setViewScriptPathSpec(':controller/:action.:suffix')
			->setViewScriptPathNoControllerSpec(':action.:suffix')
			->setViewSuffix('phtml');
		if($request->getModuleName() == 'content'){
			$view->placeholder('slug')->set($request->getControllerName());
			$request->setControllerName('content');
			if($request->getActionName()){
				$view->placeholder('subslug')->set($request->getActionName());
				$request->setActionName('index');
			}
		}
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
		$view = Zend_Registry::getInstance()->get('view');
		if($this->getResponse()->getHttpResponseCode() == 404){
		}
	}

	/**
	 * Called before Zend_Controller_Front exits its dispatch loop.
	 *
	 * @return void
	 */
	public function dispatchLoopShutdown(){}
}

