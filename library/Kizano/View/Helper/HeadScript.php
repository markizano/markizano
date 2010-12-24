<?php

/**
 * Overrides the native ZF view helper to prepend the base URL for 
 * scripts to provide script paths.
 */
class Kizano_View_Helper_HeadScript extends Zend_View_Helper_HeadScript{
	/**
	 * Prefixes a given script path with a base URL for scripts before  
	 * rendering the markup for a script element to include it.
	 *
	 * @param stdClass $item Object representing the script to render
	 * @param string $indent
	 * @param string $escapeStart
	 * @param string $escapeEnd
	 *
	 * @return string Markup for the rendered script element
	 */
	public function itemToString($item, $indent, $escapeStart, $escapeEnd){
		if (isset($item->attributes['src'])){
			$item->attributes['src'] = WEB_JS.ltrim($item->attributes['src'], '/');
		}
		$result = parent::itemToString($item, $indent, $escapeStart, $escapeEnd);
		return $result;
	}
}

