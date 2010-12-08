<?php

/**
 *	Registration specification of the Kizano_Form
 */
class Kizano_Form_Register extends Kizano_Form{

	/**
	 *	Adds the registration elements to this form.
	 *	@return Kizano_Form_Register
	 */
	public function Register(){
		$this->setName('frmRegister');
		$this->setAction("/user/register/form");
		$this->setAttrib('id', 'Register');
		$this->addPrefixPath('Kizano_Form_Plugins', 'Kizano/Forms/Plugins/', 'element');
		$this->_formName = 'register';

		$this->addField(
			'username',
			'Zend_Form_Element_Text',
			array(
				'id'				=> 'username',
				'label'				=> LANG_FORMS_USERNAME,
				'required'			=> true,
			)
		);

		$this->addField(
			'password',
			'Zend_Form_Element_Password',
			array(
				'id'				=> 'password',
				'label'				=> LANG_FORMS_PASSWORD,
				'required'			=> true,
			)
		);

		$this->addField(
			'password_v',
			'Zend_Form_Element_Password',
			array(
				'id'				=> 'password_v',
				'label'				=> LANG_FORMS_PASSWORD_VERIFY,
				'required'			=> true,
			)
		);

		$this->addField(
			'eMail',
			'Zend_Form_Element_Text',
			array(
				'id'				=> 'eMail',
				'label'				=> LANG_FORMS_EMAIL,
				'required'			=> true,
			)
		);

		$this->addField(
			'Name_First',
			'Zend_Form_Element_Text',
			array(
				'id'				=> 'fname',
				'label'				=> LANG_FORMS_NAME_FIRST,
			)
		);

		$this->addField(
			'Name_Last',
			'Zend_Form_Element_Text',
			array(
				'id'				=> 'lname',
				'label'				=> LANG_FORMS_NAME_LAST,
			)
		);

		$this->addField(
			'Address_Street_Line1',
			'Zend_Form_Element_Text',
			array(
				'id'				=> 'address_1',
				'name'				=> 'Address_Street_Line1',
				'label'				=> LANG_FORMS_ADDRESS_LINE1,
			)
		);

		$this->addField(
			'Address_Street_Line2',
			'Zend_Form_Element_Text',
			array(
				'id'				=> 'address_2',
				'name'				=> 'Address_Street_Line2',
				'label'				=> LANG_FORMS_ADDRESS_LINE2,
			)
		);

		$this->addField(
			'Address_City',
			'Zend_Form_Element_Text',
			array(
				'id'				=> 'city',
				'name'				=> 'Address_City',
				'label'				=> LANG_FORMS_ADDRESS_CITY,
			)
		);

		$this->addField(
			'Address_State',
			'Zend_Form_Element_Select',
			array(
				'id'				=> 'STATE',
				'name'				=> 'Address_State',
				'label'				=> LANG_FORMS_ADDRESS_STATE,
				'multiOptions'		=> Kizano_Strings::$STATES['US'],
			)
		);

		$this->addField(
			'States_US',
			'Zend_Form_Element_Select',
			array(
				'id'				=> 'US_STATES',
				'name'				=> 'States_US',
				'label'				=> null,
				'style'				=> 'display:none',
				'multiOptions'		=> Kizano_Strings::$STATES['US'],
			)
		);

		$this->addField(
			'States_UK',
			'Zend_Form_Element_Select',
			array(
				'id'				=> 'UK_STATES',
				'name'				=> 'States_UK',
				'label'				=> null,
				'style'				=> 'display:none',
				'mutliOptions'		=> Kizano_Strings::$STATES['UK'],
			)
		);

		$this->addField(
			'States_CA',
			'Zend_Form_Element_Select',
			array(
				'id'				=> 'CA_STATES',
				'name'				=> 'States_CA',
				'label'				=> null,
				'style'				=> 'display:none',
				'multiOptions'		=> Kizano_Strings::$STATES['CA'],
			)
		);

		$this->addField(
			'States_AT',
			'Zend_Form_Element_Select',
			array(
				'id'				=> 'AT_STATES',
				'name'				=> 'States_AT',
				'label'				=> null,
				'style'				=> 'display:none',
				'multiOptions'		=> Kizano_Strings::$STATES['AT'],
			)
		);

		$this->addField(
			'Address_Zip',
			'Zend_Form_Element_Text',
			array(
				'id'				=> 'zip',
				'label'				=> 'Zip Code:',
			)
		);

		$this->addField(
			'Address_Country',
			'Zend_Form_Element_Select',
			array(
				'label'				=> LANG_FORMS_ADDRESS_COUNTRY,
				'id'				=> 'COUNTRY',
				'value'				=>'US',
				'onchange'			=>'
				$("STATE").enable(true);
				switch(this.value){
					case &quot;US&quot; : $("STATE").innerHTML=$("US_STATES").innerHTML; break;
					case &quot;CA&quot; : $("STATE").innerHTML=$("CA_STATES").innerHTML; break;
					case &quot;GB&quot; : $("STATE").innerHTML=$("UK_STATES").innerHTML; break;
					case &quot;AU&quot; : $("STATE").innerHTML=$("AT_STATES").innerHTML; break;
					default:
						$("STATE").innerHTML=$chr(60)+"option value=&#39;&#39;"+$chr(62)+"Unavailable"+$chr(60)+"/option"+$chr(62);
						$("STATE").enable(false);
				};',
				'multiOptions'		=> Kizano_Strings::$COUNTRIES,
				'required'			=> true,
			)
		);

		$this->addField(
			'Phone',
			'Zend_Form_Element_Text',
			array(
				'label'			=> LANG_FORMS_PHONE,
				'onkeyup'		=>'this.value=/[0-9]+/.exec(this.value)',
			)
		);

		$lang = 'en'; # Later, when we have some lanugage stability, I'll change this to pull from the session instead.
		$terms = '<pre class="terms">'.file_get_contents(DIR_APPLICATION."locale/terms/$lang.txt").'</pre>';
		$this->addField(
			'terms',
			'Kizano_Form_Element_Static',
			array(
				'label'			=> $terms,
				'id'			=> 'terms',
			)
		);

		$this->addField(
			'User_Type',
			'Zend_Form_Element_Radio',
			array(
				'id'			=> 'User_Type',
				'multiOptions'	=>array(
					'Consumer'		=> LANG_FORMS_REGISTER_CONSUMER,
					'Provider'		=> LANG_FORMS_REGISTER_PROVIDER,
				),
				'required'		=> true,
			)
		);

		return $this;
	}

	/**
	 *	Validation hook to add validators to the child elements on this form.
	 *	@return void
	 */
	protected function _validate(){
		parent::_validate();
		$strlen = array('min'		=>6, 'max'		=>255);
		$this->getField('username')->addValidator(new Zend_Validate_StringLength($strlen));
		$this->getField('password')->addValidator(new Zend_Validate_StringLength($strlen));
		$this->getField('password_v')->addValidator(new Zend_Validate_StringLength($strlen));
		$this->getField('eMail')->addValidator(new Zend_Validate_EmailAddress);
		$this->getField('eMail')->addValidator(new Zend_Validate_StringLength($strlen));
		$this->getField('Phone')->addValidator(new Zend_Validate_Digits);
		$this->getField('Address_State')->addValidator(new Zend_Validate_StringLength(array('min'		=>2, 'max'		=>2)));
		$this->getField('Address_Country')->addValidator(new Zend_Validate_StringLength(array('min'		=>2, 'max'		=>2)));
		$this->getField('Address_Zip')->addValidator(new Zend_Validate_Digits);
	}
}

