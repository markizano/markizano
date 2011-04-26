<?php
/**
 *  KizanoTest
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
 *  @package    Test
 *  @copyright  Copyright (c) 2009-2011 Markizano Draconus <markizano@markizano.net>
 *  @license    http://framework.zend.com/license/new-bsd     New BSD License
 *  @author     Markizano Draconus <markizano@markizano.net>
 */

/**
 *  Assures us the functionality of the Kizano class.
 *
 *  @category   Kizano
 *  @package    Test
 *  @copyright  Copyright (c) 2009-2011 Markizano Draconus <markizano@markizano.net>
 *  @license    http://framework.zend.com/license/new-bsd     New BSD License
 *  @author     Markizano Draconus <markizano@markizano.net>
 */
class KizanoTest extends Kizano_Test_PHPUnit_ControllerTestCase
{
    /**
     *  Holds an instance of the class we are testing.
     *  
     *  @ver Kizano
     */
    protected $_model;

    /**
     *  Bootstraps this test case.
     *  
     *  @return void
     */
    public function setup()
    {
        $this->_model = Kizano::getInstance();
    }

    /**
     *  Ensures the model returns an instance of the right class.
     *  
     *  @return void
     */
    public function testGetInstance()
    {
        $this->assertTrue($this->_model instanceof Kizano, 'Failed to assert the class returns a proper instance.');
    }

    /**
     *  Asserts the method properly obtains a class and assigns it to the registry.
     *  
     *  @return void
     */
    public function testGetModel()
    {
        $mockClass = $this->getMock('ArrayObject', array(), array(ArrayObject::ARRAY_AS_PROPS), 'MockArrayObject');
    }
}

