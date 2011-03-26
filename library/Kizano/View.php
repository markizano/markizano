<?php
/**
 *	@Name: ~/includes/library/Kizano/View.php
 *	@Date: 2010-05-04
 *	@Depends: ~/includes/class/View.php
 *	@Description: The class Extension that drives the rendering scheme
 *	@Notes: Edit with care
 *	
 *	Kizano: ZF-Friendly library extensions.
 *	@CopyRight: (c) 2010 Markizano Draconus <markizano@markizano.net>
 */

/**
 *	Abstract Class to drive the way a page is rendered.
 *	@Documentation TBA
 */
class Kizano_View extends Zend_View_Abstract{

	public $config;
	protected $_blocks;
	protected $_derived;
	protected $_extends;
	protected $_data;
	protected $_refs;
	protected $_smarty;
	protected $_registry;
	protected $_stylesheets;
	protected $_scripts;
	protected $_page;

	public static function getInstance(){
		Zend_Registry::getInstance()->isRegistered('view') || Zend_Registry::getInstance()->set('view', new self());
		return Zend_Registry::getInstance()->get('view');
	}

	public function __construct(){
		$config = Zend_Registry::getInstance()->get('config');
		$this->config = $config['resources']['view'];

		$this->_smarty = new Smarty();
		$this->_smarty->template_dir		= $this->config->template_dir;
		$this->_smarty->compile_dir			= $this->config->tmp_dir;
		$this->_smarty->cache_dir			= $this->config->tmp_dir;
		$this->_smarty->config_dir			= SMARTY_DIR.'configs';
		$this->_smarty->caching				= LIVE;
		$this->_smarty->compile_check		= !LIVE;
		$this->_smarty->cache_lifetime		= LIVE? 3600: 0;
		$this->_smarty->debugging			= false;
		$this->_smarty->force_compile		= !LIVE;
		$this->_smarty->php_handling		= SMARTY_PHP_ALLOW;
		$this->_smarty->error_reporting		= error_reporting();
		$this->_smarty->register_block('block', array(&$this, 'smarty_block'));
		$this->_smarty->register_function('extends', array(&$this, 'smarty_extends'));
		$this->_extends = array();
		$this->placeholder('title')->set($_SERVER['HTTP_HOST']);

		parent::__construct($this->config);
		return $this;
	}

	public function __get($name){
		if(isset($this->_data[$name]))						return $this->_data[$name];
		elseif(isset($this->_smarty->$name))				return $this->_smarty->$name;
		elseif($this->_smarty->get_template_vars($name))	return $this->_smarty->get_template_vars($name);
		elseif($get = parent::__get($name))					return $get;
		else												return null;
	}
	public function __set($name, $val){return $this->_data[$name] = $val;}
	public function __isset($name){return isset($this->_data[$name]) || is_null(parent::__get($name));}
	public function __unset($name){unset($this->_data[$name]);return;}
	public function __toString(){return $this->fetch($this->placeholder('derived'));}
	public function __call($method, $args){
		if(method_exists($this, $method))
			return call_user_func_array($callback, $args);
		$callback = array(&$this->_smarty, $method);
		if(is_callable($callback))
			return call_user_func_array($callback, $args);
		else return parent::__call($method, $args);
	}

	public function init(){
		$this->assign_by_ref('this', $this);
	}
	public function getEngine(){return $this->_smarty;}
	public function setScriptPath($path = null){
		if(file_exists($path)){
			$this->template_dir = $path;
			$this->_smarty->template_dir = $path;
		}
		return;
	}
	public function getScriptPaths(){return array($this->_smarty->template_dir);}
	public function setBasePath($path, $prefix='index'){return $this->setScriptPath($path);}
	public function setCompilePath($path = null){$this->_smarty->compile_dir = $path; return;}

####################################################################################################

	public function assign($name, $data = null){
		if(is_string($name))
			return $this->_data[$name] = $data;
		elseif(is_array($name)){
			foreach($name as $key => $val)
				$this->_data[$key] = $val;
		}else
			throw new Kizano_Exception(sprintf('%s::%s(): First argument must be string or array. %s was passed.', __CLASS__, __FUNCTION__, getType($name)));
		return false;
	}

	public function &assign_by_ref($name, &$data){
		if(is_string($name))
			return $this->_refs[$name] =& $data;
		elseif(is_array($name)){
			foreach($name as $key => $val)
				$this->_refs[$key] =& $val;
		}else
			throw new Kizano_Exception(sprintf('%s::%s(): First argument must be string or array. %s was passed.', __CLASS__, __FUNCTION__, getType($name)));
	}

	public function fetch($resource_name){
		$this->_page = $resource_name;
		if(is_array($this->_refs) && count($this->_refs))
			foreach($this->_refs as $i => $ref)
				$this->_smarty->assign_by_ref($i, $this->_refs[$i]);
		count($this->_data) && array_map(array(&$this->_smarty, 'assign'), $this->_data);

		$ret = $this->_smarty->fetch($resource_name);

		if($resource = $this->placeholder('derived') && file_exists($this->placeholder('derived'))){
			$this->placeholder('derived')->set(false);
			$ret = $this->_smarty->fetch($resource);
		}
		return $ret;
	}
	protected function _run($t = null){return $this->fetch($t);}
	public function display($template){print $this->fetch($template);}
	public function render($template = 'index'){return $this->fetch($template);}
####################################################################################################
/*
	public function &headLink(){
		isset($this->_stylesheets) || $this->_stylesheets = new Kizano_View_Stylesheets;
		return $this->_stylesheets;
	}

	public function &headScripts(){
		isset($this->_scripts) || $this->_scripts = new Kizano_View_Scripts;
		return $this->_scripts;
	}
//*/

	/**
	 *	Processes the {block} attribute in the smarty engine
	 *	@Documentation TBA
	 */
	public function smarty_block($params, $content, &$smarty, &$repeat){
		if($repeat) return;
		$name = $params['name'];
		if(!$this->placeholder($name)){
			$this->placeholder($name)->set($content);
		}else{
			if(isset($params['overwrite'])){
				$this->placeholder($name)->set($content);
			}elseif(isset($params['prepend'])){
				$this->placeholder($name)->set($this->placeholder($name).$content);
			}elseif(isset($params['append'])){
				$this->placeholder($name)->set($content.$this->placeholder($name));
			}
		}
		return $this->placeholder($name);
	}

	/**
	 *	Processes the {extends} block in smarty templates
	 *	@Documentation TBA
	 */
	public function smarty_extends($params, &$smarty = null){
		$path = $this->_smarty->template_dir.$params['file'];
		$file = realpath($path);
		if(!empty($file) && in_array($file, $this->_extends)) return false;
		else $this->_extends[] = $file;
		if(file_exists($file)){
			if(is_file($file)){
				$this->placeholder('parent')->set(basename($this->_page));
			}else{
				# Error, the path is not a file
				throw new Kizano_Exception(sprintf('%s::%s(): `%s\'(realpath:`%s\') is not a file.', __CLASS__, __FUNCTION__, $path, $file));
			}
		}else{
			# Error, the file does not exist
			throw new Kizano_Exception(sprintf('%s::%s(): `%s\'(realpath:`%s\') does not exist.', __CLASS__, __FUNCTION__, $path, $file));
		}
		return $this->_smarty->fetch($file);
	}
}

function Kizano_View_Escape($input){
	$result = htmlEntities($input, ENT_QUOTES, 'utf-8');
	return $result;
}

