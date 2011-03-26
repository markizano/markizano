<?php
/**
 *  DebugController
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
 *  @package    Kizano_Application
 *  @copyright  Copyright (c) 2009-2011 Markizano Draconus <markizano@markizano.net>
 *  @license    http://framework.zend.com/license/new-bsd     New BSD License
 *  @author     Markizano Draconus <markizano@markizano.net>
 */

/**
 *  General Debugging controller. I plotted this out because it allows me to debug things
 *    in the context of the application :D
 *    
 *  @category   Kizano
 *  @package    Kizano_Application
 *  @copyright  Copyright (c) 2009-2011 Markizano Draconus <markizano@markizano.net>
 *  @license    http://framework.zend.com/license/new-bsd     New BSD License
 *  @author     Markizano Draconus <markizano@markizano.net>
 */
class DebugController extends Kizano_Controller_Action
{

    protected $_test;

    public function init()
    {
        # If I forget to disable this controller...
        $env = array('testing', 'local');
        if ($_SERVER['REMOTE_ADDR'] != $_SERVER['SERVER_ADDR'] || !in_array(ENVIRONMENT, $env)) {
            $this->_helper->redirector->gotoUrl('/');
        }

        $this->view->debug = 'Please input something.';

        if ($this->_request->getActionName() != 'ajax') {
            #$this->_helper->layout->disableLayout();
            #$this->_helper->viewRenderer->setNoRender(true);
        }
    }

    /**
     *  Home page debugging action.
     *  
     *  @return void
     */
    public function indexAction()
    {
        $this->_disableLayout();
        print "debugging...";

        return;
        $table = Kizano::getTable('UserUsers');
        var_dump($table);
    }

    /**
     *    Allows for testing in the context of the application using AJAX requests/responses.
     *    
     *    @return void
     */
    public function ajaxAction()
    {
        $this->view->ajax = <<<JQUERY
<input type="button" value="AJAX" id="ajax" />
<div id="output"></div>
<script type="text/javascript">//<![CDATA[
(function($){
    $("#ajax").click(function(){
        try{
        $.ajax({
            type    : "POST",
            url     : '/debug/index',
            data    : "/query=true&test=value",
            dataType: "json",
            success : myNotify
        });
        }catch(e){console.log(e);}
    });
    function myNotify(data){
        console.log(data);
        $("#output")[0].html(data.data);
    }
})(jQuery)
//]]></script>
JQUERY
;
    }

    /**
     *    Dumps the session data with a little bit of param control.
     *    
     *    @return void
     */
    public function sessionAction()
    {
        if ($this->_request->getParam('destroy') !== null) {
            session_destroy();
            print "Session destroyed.<br />\n";
        } else {
            var_dump($_SESSION);
        }
    }
}

