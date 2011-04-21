<?php
/**
 *  Kizano_View_Smarty
 *
 *  LICENSE
 *
 *  This source file is subject to the new BSD license that is bundled
 *  with this package in the file LICENSE.txt.
 *  It is also available through the world-wide-web at this URL:
 *  http://framework.zend.com/license/new-bsd
 *  If you did not receive a copy of the license and are unable to
 *  obtain it through the world-wide-web, please send an email
 *  to license@zend.com so we can send you a copy immediately.
 *
 *  @category   Kizano
 *  @package    View
 *  @copyright  Copyright (c) 2009-2011 Markizano Draconus <markizano@markizano.net>
 *  @license    http://framework.zend.com/license/new-bsd     New BSD License
 *  @author     Markizano Draconus <markizano@markizano.net>
 */

/**
 *  Replaces Zend_View with a smarty engine instead. Uses the proxy pattern to access smarty functions/methods.
 *
 *  @category   Smarty
 *  @package    View
 *  @copyright  Copyright (c) 2009-2011 Markizano Draconus <markizano@markizano.net>
 *  @license    http://framework.zend.com/license/new-bsd     New BSD License
 *  @author     Markizano Draconus <markizano@markizano.net>
 */
class Kizano_View_Smarty extends Zend_View_Abstract
{
    /**
     *  Smarty configurations.
     *  
     *  @var Array
     */
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

    public function __construct($options = array())
    {
        if ($options instanceof Zend_Config) {
            $options = $options->toArray();
        }
        if (!is_array($options)) {
            throw new InvalidArgumentException('Argument 1 ($options) expected to be an array or instance of Zend_Config');
        }

        $live = (Zend_Registry::get('application')->getEnvironment() == 'production');

        $this->_smarty = new Smarty();
        $this->_smarty->template_dir            = $this->config->template_dir;
        $this->_smarty->compile_dir             = $this->config->tmp_dir;
        $this->_smarty->cache_dir               = $this->config->tmp_dir;
        $this->_smarty->config_dir              = SMARTY_DIR . 'configs';
        $this->_smarty->caching                 = $live;
        $this->_smarty->compile_check           = !$live;
        $this->_smarty->cache_lifetime          = $live? 3600: 0;
        $this->_smarty->debugging               = false;
        $this->_smarty->force_compile           = !$live;
        $this->_smarty->php_handling            = SMARTY_PHP_ALLOW;
        $this->_smarty->error_reporting        = error_reporting();
        $this->_smarty->register_block('block', array(&$this, 'smarty_block'));
        $this->_smarty->register_function('extends', array(&$this, 'smarty_extends'));
        $this->_extends = array();
        $this->placeholder('title')->set($_SERVER['HTTP_HOST']);

        $this->assign_by_ref('this', $this);
        parent::__construct($this->config);
        return $this;
    }

    /**
     *  Magic function to obtain any property.
     *  
     *  @param String   $name   The key to obtain
     *  
     *  @return Mixed
     */
    public function __get($name) {
        if (isset($this->_data[$name])) {
            return $this->_data[$name];
        } elseif (isset($this->_smarty->$name)) {
            return $this->_smarty->$name;
        } elseif ($get = $this->_smarty->get_template_vars($name)) {
            return $get;
        } elseif ($get = parent::__get($name)) {
            return $get;
        } else {
            return null;
        }
    }

    /**
     *  Magic method to set variables.
     *  
     *  @param String   $name   The key name to use.
     *  @param Mixed    $val    The value to assign.
     *  
     *  @return void
     */
    public function __set($name, $val = null)
    {
        return $this->_data[$name] = $val;
    }

    /**
     *  Magic method to determine if a variable is set.
     *  
     *  @param String   $name   The key name to use.
     *  
     *  @return boolean
     */
    public function __isset($name)
    {
        if (isset($this->_data[$name])) {
            return true;
        }

        if (isset($this->_smarty->$name)) {
            return true;
        }

        return parent::__isset($name);
    }

    /**
     *  Unsets a variable.
     *  
     *  @return void
     */
    public function __unset($name)
    {
        unset($this->_data[$name]);
    }

    /**
     *  Returns this object as string.
     *  
     *  @return string
     */
    public function __toString()
    {
        return $this->fetch($this->placeholder('derived'));
    }

    /**
     *  Magic method to call a method that doesn't exist.
     *  
     *  @param String   $method     The method to call.
     *  @param Array    $args       The arguments to pass to the method.
     *  
     *  @return Mixed.
     */
    public function __call($method, $args)
    {
        if (method_exists($this, $method)) {
            return call_user_func_array(array(&$this, $method), $args);
        }

        $callback = array(&$this->_smarty, $method);
        if (is_callable($callback)) {
            return call_user_func_array($callback, $args);
        }

        return parent::__call($method, $args);
    }

    /**
     *  Gets the current rendering engine.
     *  
     *  @return Smarty
     */
    public function getEngine()
    {
        return $this->_smarty;
    }

    /**
     *  Sets the path for executing scripts.
     *  
     *  @param String   $path   The path of a script to render.
     *  
     *  @return Kizano_View_Smarty
     */
    public function setScriptPath($path = null) {
        if (!file_exists($path) || !is_readable($path) || !is_file($path)) {
            throw new Kizano_View_Exception("Cannot stat `$path'. No such file or not readable.");
        }

        $this->_smarty->template_dir = $path;
        return $this;
    }

    /**
     *  Gets a list of script paths.
     *  
     *  @return Array
     */
    public function getScriptPaths()
    {
        return array($this->_smarty->template_dir);
    }

    /**
     *  Sets the base path.
     *  
     *  @param String   $path   The path to assign.
     *  @param String   $prefix The prefix to use.
     *  
     *  @return Kizano_View_Smarty
     */
    public function setBasePath($path, $prefix = 'index')
    {
        return $this->setScriptPath($path);
    }

    /**
     *  Sets the Smarty compile directory.
     *  
     *  @param String   $path   The path to assign.
     *  
     *  @return Kizano_View_Smarty
     */
    public function setCompilePath($path = null)
    {
        if (!file_exists($path) || !is_writable($path) || !is_dir($path)) {
            throw new Kizano_View_Exception("Cannot stat `$path' not a directory or is unwritable.");
        }

        $this->_smarty->compile_dir = $path;
        return $this;
    }

    /**
     *  Assigns a value or set of values to this instance of the class.
     *  
     *  @return Mixed
     */
    public function assign($name, $data = null) {
        if (is_string($name))
            return $this->_data[$name] = $data;
        elseif (is_array($name)) {
            foreach ($name as $key => $val)
                $this->_data[$key] = $val;
        }else
            throw new Kizano_Exception(sprintf('%s::%s(): First argument must be string or array. %s was passed.', __CLASS__, __FUNCTION__, getType($name)));
        return $this;
    }

    /**
     *  Assigns a variable by reference.
     *  
     *  @param String   $name   The name key to use.
     *  @param Mixed    $data   The data stuff to use.
     *  
     *  @return Kizano_View_Smarty
     */
    public function &assign_by_ref($name, &$data) {
        if (is_string($name)) {
            return $this->_refs[$name] =& $data;
        } elseif (is_array($name)) {
            foreach ($name as $key => $val) {
                $this->_refs[$key] =& $val;
            }
        } else {
            throw new InvalidArgumentException(
                sprintf('%s::%s(): First argument must be string or array. %s was passed.',
                    __CLASS__,
                    __FUNCTION__,
                    getType($name)
                )
            );
        }
    }

    /**
     *  Fetches a resource.
     *  
     *  @param String   $resource_name  The name of the resource to fetch.
     *  
     *  @return Mixed
     */
    public function fetch($resource_name) {
        $this->_page = $resource_name;
        if (is_array($this->_refs) && count($this->_refs))
            foreach ($this->_refs as $i => $ref)
                $this->_smarty->assign_by_ref($i, $this->_refs[$i]);
        count($this->_data) && array_map(array(&$this->_smarty, 'assign'), $this->_data);

        $ret = $this->_smarty->fetch($resource_name);

        if ($resource = $this->placeholder('derived') && file_exists($this->placeholder('derived'))) {
            $this->placeholder('derived')->set(false);
            $ret = $this->_smarty->fetch($resource);
        }
        return $ret;
    }

    protected function _run($t = null)
    {
        return $this->fetch($t);
    }

    public function display($template)
    {
        print $this->fetch($template);
    }

    public function render($template = 'index')
    {
        return $this->fetch($template);
    }

    /**
     *    Processes the {block} attribute in the smarty engine
     *    @Documentation TBA
     */
    public function smarty_block($params, $content, &$smarty, &$repeat) {
        if ($repeat) return;
        $name = $params['name'];
        if (!$this->placeholder($name)) {
            $this->placeholder($name)->set($content);
        } else {
            if (isset($params['overwrite'])) {
                $this->placeholder($name)->set($content);
            } elseif (isset($params['prepend'])) {
                $this->placeholder($name)->set($this->placeholder($name).$content);
            } elseif (isset($params['append'])) {
                $this->placeholder($name)->set($content.$this->placeholder($name));
            }
        }
        return $this->placeholder($name);
    }

    /**
     *    Processes the {extends} block in smarty templates
     *    @Documentation TBA
     */
    public function smarty_extends($params, &$smarty = null) {
        $path = $this->_smarty->template_dir.$params['file'];
        $file = realpath($path);

        if (!empty($file) && in_array($file, $this->_extends)) {
            return false;
        } else {
            $this->_extends[] = $file;
        }

        if (file_exists($file)) {
            if (is_file($file)) {
                $this->placeholder('parent')->set(basename($this->_page));
            } else {
                # Error, the path is not a file
                throw new Kizano_View_Exception(sprintf('%s::%s(): `%s\'(realpath:`%s\') is not a file.', __CLASS__, __FUNCTION__, $path, $file));
            }
        } else {
            # Error, the file does not exist
            throw new Kizano_View_Exception(sprintf('%s::%s(): `%s\'(realpath:`%s\') does not exist.', __CLASS__, __FUNCTION__, $path, $file));
        }

        return $this->_smarty->fetch($file);
    }
}

