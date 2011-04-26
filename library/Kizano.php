<?php
/**
 *  Kizano
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
 *  @package    Kizano
 *  @copyright  Copyright (c) 2009-2011 Markizano Draconus <markizano@markizano.net>
 *  @license    http://framework.zend.com/license/new-bsd     New BSD License
 *  @author     Markizano Draconus <markizano@markizano.net>
 */

/**
 *  Central access point to all that which is Kizano.
 *
 *  @category   Kizano
 *  @package    Kizano
 *  @copyright  Copyright (c) 2009-2011 Markizano Draconus <markizano@markizano.net>
 *  @license    http://framework.zend.com/license/new-bsd     New BSD License
 *  @author     Markizano Draconus <markizano@markizano.net>
 */
class Kizano
{
    /**
     *  Singleton.
     *  
     *  @var Kizano
     */
    protected static $_instance;

    /**
     *  Holds the container for which we will store the models.
     *  
     *  @var ArrayObject
     */
    protected $_container;

    /**
     *  Gets any model by name. Allows for inter-dependency injection.
     *  
     *  @param String   $name   The name of the class to obtain.
     *  @param Array    $args   The arguments to pass to the construct.
     *  
     *  @return Mixed
     */
    public static function getModel($name, array $args = array())
    {
        $self = self::getInstance();
        if (isset($self->getContainer()->$name)) {
            return $self->getContainer()->$name;
        }

        if (!class_exists($name)) {
            throw new RuntimeException("Cannot stat `$name'. No such class.");
        }

        $reflect = new ReflectionClass($name);
        if ($reflect->isInstantiable()) {
            $class = $reflect->newInstanceArgs($args);
            $this->getContainer()->$name = $class;
            return $class;
        }
        // Iterate through a series of possible methods that can construct a class instance.
        foreach (array('getInstance', 'factory', 'get') as $method) {
            if ($reflect->hasMethod($method)) {
                $class = call_user_func_array(array($name, $method), $args);
                $this->getContainer()->$name = $class;
                return $class;
            }
        }

        // Should we throw an exception here?
        // throw new Kizano_Exception("Could not instantiate `$name'.");
        return false;
    }

    /**
     *  Singleton.
     *  
     *  @return Kizano
     */
    public static function getInstance()
    {
        if (empty(self::$_instance)) {
            self::$_instance = new self;
        }

        return self::$_instance;
    }

    /**
     *  Gets the container which will manage our class instances.
     *  
     *  @return ArrayObject
     */
    public function getContainer()
    {
        if (empty($this->_container)) {
            $this->_container = Zend_Registry::getInstance();
        }

        return $this->_container;
    }

    /**
     *  Allows us to inject a container.
     *  
     *  @param ArrayObject   $container   The container to inject
     *  
     *  @return Kizano
     */
    public function setContainer(ArrayObject $container)
    {
        $this->_container = $container;
        return $this;
    }
}

