<?php
/**
 *	@Name: ~/library/Kizano/Form/Login.php
 *	@Depends: ~/library/Kizano/Form.php
 *	@Description: Implements the login form.
 *	@Notes: Edit with care
 *	
 *	Kizano: ZF-Friendly library extensions.
 *	@CopyRight: (c) 2010 Markizano Draconus <markizano@markizano.net>
 */

class Kizano_Form_Login extends Kizano_Form
{

	/**
	 *	Implements the user login form.
	 *	@return Kizano_Form
	 */
	public function Login()
	{
		$this->setName('frmLogin');
		$this->setAction(WEB_ROOT."user/user/login");
		$this->setAttrib('id', 'Login');
		$this->addPrefixPath('Kizano_Form_Plugins', 'Kizano/Forms/Plugins/', 'element');
		$this->_formName = 'login';

		$this->addField(
			'username',
			'Zend_Form_Element_Text',
			array(
				'id'		=> 'username',
				'label'		=> LANG_FORMS_USERNAME,
			)
		);

		$this->addField(
			'password',
			'Zend_Form_Element_Password',
			array(
				'id'		=> 'password',
				'label'		=> LANG_FORMS_PASSWORD,
			)
		);

		$this->addField(
			'auth',
			'Zend_Form_Element_Hidden',
			array(
				'id'		=> 'auth',
				'value'		=> 1,
			)
		);

		return $this;
	}

	/**
	 *	Attaches validators to this form.
	 *	@return void
	 */
	protected function _validate()
	{
		parent::_validate();
		$this->getField('auth')->addValidator(new Kizano_Validate_Auth);
	}
}

