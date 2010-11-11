<?php

/**
 *	@Name: ~/library/Kizano/Forms/Element/Static.php
 *	@Date: 2010-09-24/14:07
 *	@Depends: ~/includes/library/Zend/Form/Element/Xhtml.php
 *	@Description: Placeholder for just text in a form, for extra description.
 *	
 *	OSASH: Online Services Auction SuperHighway
 *	@CopyRight: (c) 2010 Mark Harris
 */

class Kizano_Forms_Element_Static extends Zend_Form_Element{
	public $helper = 'FormNote';

	public function init(){
		$this->clearDecorators();
	}
}


