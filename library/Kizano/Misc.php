<?php

/**
 *	Namespace for miscelaneous functions that would normally be free-floating.
 */
class Kizano_Misc{

	/**
	 *	Gets a HTML-printable string representation of the current backtrace.
	 *	@return		String	A HTML-printable backtrace
	 */
	public static function getBacktrace(){
		$backtrace = self::backtrace();
		array_shift($backtrace);
		$result = null;
		if(count($backtrace))
			foreach($backtrace as $back){
				isset($back['class']) || $back['class'] = 'Static';
				isset($back['type']) || $back['type'] = '::';
				$result .= "&lt;<span style='color:#CC0000;'>$back[file]</span>:$back[line]&gt;&nbsp;".
					"<span style='color:#0000AA;'>$back[class]</span>$back[type]".
					"<span style='color:#0000AA;'>$back[function]</span>("
				;
				$comma = false;
				if(count($back['args']))
					foreach($back['args'] as $args){
						$result .= $comma? ', ': null;
						$comma || $comma = true;
						if(is_string($args)){
							$result .= "<span style='color:#CC0000;'>'$args'</span>";
						}elseif(is_numeric($args)){
							$type = gettype($args);
							$result .= "(<span style='color:#00CC00;'>$type</span>) $args";
						}elseif(is_array($args)){
							$type = gettype($args);
							$args = print_r($args, true);
							$result .= "(<span style='color:#00CC00;'>$type</span>) $args";
						}elseif(is_object($args)){
							$type = gettype($args);
							if(is_callable(array($args, '__toString'))){
								$args = $args->__toString();
							}else{
								$args = get_class($args);
							}
							$result .= "(<span style='color:#00CC00;'>$type</span>) $args";
						}elseif(is_bool($args)){
							$args = $args? 'true': 'false';
							$result .= "(<span style='color:#00CC00;'>boolean</span>) $args";
						}elseif(is_null($args)){
							$result .= "<span style='color:#CC0000;'>null</span>";
						}else{
							$type = gettype($args);
							$result .= "(<span style='color:#00CC00;'>$type</span>) [object]";
						}
					}
				$result .= ");<br />\n";
			}
		return $result;
	}

	/**
	 *      Returns a custom-created backtrace. One that doesn't include the dumping of irrelevant objects.
	 *      @return	 Array   The [corrected] backtrace
	 */
	public static function backtrace(){
		$debug = debug_backtrace();
		array_shift($debug);
		foreach($debug as $i => $deb){
			unset($debug[$i]['object']);
			foreach($deb['args'] as $k => $d){
				is_object($d) && $debug[$i]['args'][$k] = '(<span style="color:#00CC00;">object</span>)'.get_class($d);
			}
		}
		return($debug);
	}
}

