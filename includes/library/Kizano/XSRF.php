<?php
/**
 *	Name: ~/includes/Hack/XSRF.php
 *	By: Mark Harris
 *	Description: Anti-XSRF Attack Strategies
 *	Depends: ~/etc/Osash.inc, ~/HTML/[*]/header.php
 *	Notes:
 *	
 *	Online Services Auction SuperHighway (osash)
 *	CopyRight (c) 2010 Mark Harris
 */

$_ENV['XSRF'] = array();
$_ENV['XSRF']['token'] = base64_encode(hash('haval128,4', Kizano_Strings::strRandHex(16)));
$_ENV['XSRF']['chip'] = subStr($_ENV['XSRF']['token'], strLen($_ENV['XSRF']['token']) / 2);
$_ENV['XSRF']['cracker'] = subStr($_ENV['XSRF']['token'], 0, strLen($_ENV['XSRF']['token']) / 2);

class Kizano_XSRF{
	# Anti-XSRF Attack Strategy
	function isXSRF(){
		if(isset($_SESSION['token'])){
			if(!isset($_COOKIE['cracker']) || (!isset($_ENV['_REQ']['chip']) && !isset($_POST['custom']))){
				define('XSRF', true, true);
				return true;
			}if($_SESSION['token'] != $_COOKIE['cracker'].(isset($_POST['custom'])? $_POST['custom']: $_ENV['_REQ']['chip'])){
				define('XSRF', true, true);
				return true;
			}
			setCookie('cracker', false, 1, WEB_ROOT, $_SERVER['HTTP_HOST'], isset($_SERVER['HTTPS']), true);
			Kizano_Misc::_null($_SESSION['token']);
		}
		return false;
	}

	function setXSRF(){
		$_SESSION['token'] = $_ENV['XSRF']['token'];
		setCookie('cracker', $_ENV['XSRF']['cracker'], $_SERVER['REQUEST_TIME'] + CookieLife, WEB_ROOT, $_SERVER['HTTP_HOST'], isset($_SERVER['HTTPS']), true);
		return null;
	}
}

