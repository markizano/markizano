<?php

class Kizano_Layout_Plugins_Layout extends Zend_Controller_Plugin_Abstract{

	/**
	 *	Keeps the postDispatch function from iterating and printing more than one layout.
	 */
	protected $_postDispatch = false;

	protected $_layoutActionHelper = null;

	/**
	 * @var Zend_Layout
	 */
	protected $_layout;

	/**
	 * Constructor
	 *
	 * @param  Zend_Layout $layout
	 * @return void
	 */
	public function __construct(Zend_Layout $layout = null){
		if (!is_null($layout)){
			$this->setLayout($layout);
		}
	}

	/**
	 * Retrieve layout object
	 *
	 * @return Zend_Layout
	 */
	public function getLayout(){
		return $this->_layout;
	}

	/**
	 * Set layout object
	 *
	 * @param  Zend_Layout $layout
	 * @return Zend_Layout_Controller_Plugin_Layout
	 */
	public function setLayout(Zend_Layout $layout){
		$this->_layout = $layout;
		return $this;
	}

	/**
	 * Set layout action helper
	 *
	 * @param  Zend_Layout_Controller_Action_Helper_Layout $layoutActionHelper
	 * @return Zend_Layout_Controller_Plugin_Layout
	 */
	public function setLayoutActionHelper(Zend_Layout_Controller_Action_Helper_Layout $layoutActionHelper){
		$this->_layoutActionHelper = $layoutActionHelper;
		return $this;
	}

	/**
	 * Retrieve layout action helper
	 *
	 * @return Zend_Layout_Controller_Action_Helper_Layout
	 */
	public function getLayoutActionHelper(){
		return $this->_layoutActionHelper;
	}

	/**
	 *	Pre Dispatch hook - determine the user and which layout to use
	 *	@param request		Zend_Controller_Request_Abstract	The Zend Request object (or an instance of)
	 *	@return void
	 */
	public function preDispatch(Zend_Controller_Request_Abstract $request){
		$reg = Zend_Registry::getInstance();
		$layout = Zend_Registry::getInstance()->get('layout');
		if($request->getModuleName() == 'admin'){
			$layout->setLayoutPath(DIR_APPLICATION.'layouts'.DS.'admin');
		}else{
			$layout->setLayoutPath(DIR_APPLICATION.'layouts'.DS.'site');
		}
	}

	/**
	 * postDispatch() plugin hook -- render layout
	 *
	 * @param  Zend_Controller_Request_Abstract $request
	 * @return void
	 */
	public function postDispatch(Zend_Controller_Request_Abstract $request){
		# If we've already post-dispatched, then we shouldn't execute this function
		if($this->_postDispatch)
			return null;
		else
			$this->_postDispatch = true;
		$layout = Zend_Registry::getInstance()->get('layout');
		$helper = $this->getLayoutActionHelper();

		// Return early if forward detected
		if (!$request->isDispatched()
			|| $this->getResponse()->isRedirect()
			|| (
				$layout->getMvcSuccessfulActionOnly()
				&& (!empty($helper) && !$helper->isActionControllerSuccessful())
			)
		){
			return;
		}
		// Return early if layout has been disabled
		if (!$layout->isEnabled()){
			return;
		}

		$response   = $this->getResponse();
		$content	= $response->getBody(true);
		$contentKey = $layout->getContentKey();
		if (isset($content['default'])){
			$content[$contentKey] = $content['default'];
		}
		if ('default' != $contentKey){
			unset($content['default']);
		}
		$layout->assign($content);
		$fullContent = null;
		$obStartLevel = ob_get_level();
		try{
			$fullContent = $layout->render();
			$response->setBody($fullContent);
		} catch (Exception $e){
			while (ob_get_level() > $obStartLevel){
				$fullContent .= ob_get_clean();
			}
			$request->setParam('layoutFullContent', $fullContent);
			$request->setParam('layoutContent', $layout->content);
			$response->setBody(null);
			throw $e;
		}
	}
	/**
	 * Called before Zend_Controller_Front exits its dispatch loop.
	 *
	 * @return void
	 */
	public function dispatchLoopShutdown(){}
}

