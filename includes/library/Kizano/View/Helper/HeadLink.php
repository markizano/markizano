<?php

/**
 * Overrides the native ZF view helper to prepend the base URL for
 * stylesheets to provide stylesheet paths.
 */
class Kizano_View_Helper_HeadLink extends Zend_View_Helper_HeadLink{
	/**
	 * Prefixes a given stylesheet path with a base URL for stylesheets
	 * before rendering the markup for a link element to include it.
	 *
	 * @param stdClass $item Object representing the stylesheet to render
	 * @return string Markup for the rendered link element
	 */
	public function itemToString(stdClass $item){
		if($this->_isAdminPage(Zend_Controller_Front::getInstance()->getRequest())){
			$item->href = WEB_CSS.ltrim($item->href, '/');
		}
		return parent::itemToString($item);
	}

	private function _isAdminPage($request){
		if ($request->getParam('module') != 'public' && $request->getParam('controller') != 'public' && !$request->isXmlHttpRequest()){
			return true;
		}
	}
}

