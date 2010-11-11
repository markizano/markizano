<?php
/**
 *	@Name: ~/includes/library/Kizano/Forms.php
 *	@Depends: ~/includes/library/Zend/Form.php
 *	@Description: Main Form class
 *	@Notes: Edit with Care
 *
 *	Skillet Cafe
 *	@CopyRight: (c) 2010 Markizano Draconus <markizano@markizano.net>
 */

class Kizano_Forms extends Zend_Form{

	# Holds the name of the current form
	protected $_form;

	# Holds the fields to render in the form
	protected $_fields;

	# Default configuration to inject into the form
	protected $_defaults = array();

	# @Zend_View instance to help with the rendering
	protected $view;

	/**
	 *	Generates a new instance of this formset
	 *	@param	Zend_Config		options		(Optional) Zend_Form Configuration options
	 *	@return void
	 */
	public function __construct($options = null){
		parent::__construct($options);
		if(is_array($options) && count($options)){
			$this->_defaults = Current($options);
			parent::__construct(array());
		}else
			parent::__construct($options);
		$this->view = Zend_Registry::getInstance()->get('view');
		$this->setName('Default_Form');
		$this->setAction('');
		$this->setDisableLoadDefaultDecorators(true);
		$this->clearDecorators();
		$this->_fields = new stdClass();
		return $this;
	}

	/**
	 *	Creates Login Elements and attaches them to this instance of a form.
	 *	@return		Zend_Forms_Login		Login Form
	 */
	public function userLogin(){
		$this->setName('frmLogin');
		$this->setAction('/Login/');
		$this->setAttrib('id', 'frmLogin');
		$this->_form = 'login';

		$this->_fields->UserName = new Zend_Form_Element_Text(
			'UserName',
			new Zend_Config(array(
				'id'=>'UserName',
				'label'=>LANG_FORMS_USERNAME,
			), true)
		);
		$this->_fields->UserName
			->addValidator('NotEmpty')
			->setErrorMessages(array(LANG_FORMS_ERROR_USERNAME, LANG_FORMS_ERROR_USERNAME_LEN));

		$this->_fields->Password = new Zend_Form_Element_Password('Password');
		$this->_fields->Password
			->setLabel(LANG_FORMS_PASSWORD)
			->addValidator('NotEmpty')
			->setErrorMessages(array(LANG_FORMS_ERROR_PASSWORD, LANG_FORMS_ERROR_PASSWORD_LEN));
		return $this;
	}

	public function adminContent(){
		$this->setName('frmContent');
		$this->setAction("/content/admin/list");
		$this->setAttrib('id', 'frmContent');
		$this->_form = __FUNCTION__;

		$this->_fields->title = new Zend_Form_Element_Text(
			'title',
			new Zend_Config(array(
				'id'=>'title',
				'label'=>'Title:',
			), true)
		);
		$this->_fields->title->addValidator(new Zend_Validate_Alpha());

		$this->_fields->content = new Zend_Form_Element_Textarea(
			'_CONTENT',
			new Zend_Config(array(
				'id'=>'_CONTENT',
				'label'=>'Content',
				'class'=>'ckeditor',
			), true)
		);

		$this->_fields->publish = new Zend_Form_Element_Checkbox(
			'publish',
			new Zend_Config(array(
				'id'=>'publish',
				'label'=>'Live?',
			), true)
		);
		$this->_fields->publish->addValidator(new Zend_Validate_Int());

		if(!empty($this->_defaults)){
			isset($this->_defaults['title']) && $this->_fields->title->setValue($this->_defaults['title']);
			isset($this->_defaults['content']) && $this->_fields->content->setValue($this->_defaults['content']);
			isset($this->_defaults['publish']) && $this->_fields->publish->setValue($this->_defaults['publish']);
			isset($this->_defaults['id']) && $this->_fields->id = new Zend_Form_Element_Hidden(
				'id',
				new Zend_Config(array(
					'id'=>'id',
					'value'=>$this->_defaults['id']
				), true)
			);
		}

		return $this;
	}

	public function Register(){
		$this->setName('frmRegister');
		$this->setAction("/user/register/register");
		$this->setAttrib('id', 'Register');
		$this->addPrefixPath('Kizano_Forms_Plugins', 'Kizano/Forms/Plugins/', 'element');
		$this->_form = 'register';

		$this->_fields->username = new Zend_Form_Element_Text(
			'username',
			new Zend_Config(array(
				'id'=>'username',
				'label'=>LANG_FORMS_USERNAME,
				'required' => true,
			), true)
		);

		$this->_fields->password = new Zend_Form_Element_Password(
			'password',
			new Zend_Config(array(
				'id'=>'password',
				'label'=>LANG_FORMS_PASSWORD,
				'required' => true,
			), true)
		);

		$this->_fields->password_v = new Zend_Form_Element_Password(
			'password_v',
			new Zend_Config(array(
				'id'=>'password_v',
				'label'=>LANG_FORMS_PASSWORD_VERIFY,
				'required' => true,
			), true)
		);

		$this->_fields->eMail = new Zend_Form_Element_Text(
			'eMail',
			new Zend_Config(array(
				'id'=>'eMail',
				'label'=>LANG_FORMS_EMAIL,
				'required' => true,
			), true)
		);

		$this->_fields->Name_First = new Zend_Form_Element_Text(
			'Name_First',
			new Zend_Config(array(
				'id'=>'fname',
				'label'=>LANG_FORMS_NAME_FIRST,
			), true)
		);

		$this->_fields->Name_Last = new Zend_Form_Element_Text(
			'Name_Last',
			new Zend_Config(array(
				'id'=>'lname',
				'label'=>LANG_FORMS_NAME_LAST,
			), true)
		);

		$this->_fields->Address_Street_Line1 = new Zend_Form_Element_Text(
			'Address_Street_Line1',
			new Zend_Config(array(
				'id'=>'address_1',
				'name'=>'Address_Street_Line1',
				'label'=>LANG_FORMS_ADDRESS_LINE1,
			), true)
		);

		$this->_fields->Address_Street_Line2 = new Zend_Form_Element_Text(
			'Address_Street_Line2',
			new Zend_Config(array(
				'id'=>'address_2',
				'name'=>'Address_Street_Line2',
				'label'=>LANG_FORMS_ADDRESS_LINE2,
			), true)
		);

		$this->_fields->Address_City = new Zend_Form_Element_Text(
			'Address_City',
			new Zend_Config(array(
				'id'=>'city',
				'name'=>'Address_City',
				'label'=>LANG_FORMS_ADDRESS_CITY,
			), true)
		);

		$this->_fields->Address_State = new Zend_Form_Element_Select(
			'Address_State',
			new Zend_Config(array(
				'id'=>'STATE',
				'name'=>'Address_State',
				'label'=>LANG_FORMS_ADDRESS_STATE,
				'multiOptions' => $_ENV['US_STATES'],
			), true)
		);

		$this->_fields->States_US = new Zend_Form_Element_Select(
			'States_US',
			new Zend_Config(array(
				'id'=>'US_STATES',
				'name'=>'States_US',
				'label'=>null,
				'style'=>'display:none',
				'multiOptions' => $_ENV['US_STATES'],
			), true)
		);

		$this->_fields->States_UK = new Zend_Form_Element_Select(
			'States_UK',
			new Zend_Config(array(
				'id'=>'UK_STATES',
				'name'=>'States_UK',
				'label'=>null,
				'style'=>'display:none',
				'mutliOptions' => $_ENV['UK_STATES'],
			), true)
		);

		$this->_fields->States_CA = new Zend_Form_Element_Select(
			'States_CA',
			new Zend_Config(array(
				'id'=>'CA_STATES',
				'name'=>'States_CA',
				'label'=>null,
				'style'=>'display:none',
				'multiOptions' => $_ENV['CA_STATES'],
			), true)
		);

		$this->_fields->States_AT = new Zend_Form_Element_Select(
			'States_AT',
			new Zend_Config(array(
				'id'=>'AT_STATES',
				'name'=>'States_AT',
				'label'=>null,
				'style'=>'display:none',
				'multiOptions' => $_ENV['AT_STATES'],
			), true)
		);

		$this->_fields->Address_Zip = new Zend_Form_Element_Text(
			'Address_Zip',
			new Zend_Config(array(
				'id'=>'zip',
				'label'=>'Zip Code:',
			), true)
		);

		$this->_fields->Address_Country = new Zend_Form_Element_Select(
			'Address_Country',
			new Zend_Config(array(
				'label'=>LANG_FORMS_ADDRESS_COUNTRY,
				'id'=>'COUNTRY',
				'value'=>'US',
				'onchange'=>'
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
				'multiOptions' => $_ENV['COUNTRIES'],
				'required' => true,
			), true)
		);

		$this->_fields->Phone = new Zend_Form_Element_Text(
			'Phone',
			new Zend_Config(array(
				'label'=>LANG_FORMS_PHONE,
				'onkeyup'=>'this.value=/[0-9\-\.]+/.exec(this.value)',
			), true)
		);

		$lang = 'en'; # Later, when we have some lanugage stability, I'll change this to pull from the session instead.
		$terms = '<pre class="terms">'.file_get_contents(DIR_APPLICATION."locale/terms/$lang.txt").'</pre>';
		$this->_fields->terms = new Kizano_Forms_Element_Static(
			'terms',
			new Zend_Config(array(
				'label'=>$terms,
				'id'=>'terms',
			), true)
		);

		$this->_fields->User_Type = new Zend_Form_Element_Radio(
			'User_Type',
			new Zend_Config(array(
				'id'=>'User_Type',
				'multiOptions'=>array(
					'Consumer'=>LANG_FORMS_REGISTER_CONSUMER,
					'Provider'=>LANG_FORMS_REGISTER_PROVIDER,
				),
				'required' => true,
			), true)
		);

		return $this;
	}

	/**
	 *	Validates the form.
	 *	@param	input	The input to validate
	 *	@return boolean
	 */
	public function isValid($input = array()){
		var_dump($input);die;
		$result = parent::isValid($input);
		switch(strToLower($this->_form)){
			case 'admincontent':
			break; case 'login':
			break; case 'register':
				foreach(array(
					'username', 'password', 'eMail', 'Name_First', 'Name_Last',
					'Address_Street_Line1', 'Address_Street_Line2', 'Address_City',
					'Address_State', 'Address_Zip', 'Country', 'Phone', 'User_Type'
				) as $field){
					if(!isset($input[$field])){
						$this->_fields->$field->setError("$field: Missing input");
						return false;
					}
				}
			break; default:
				
		}
	}

	/**
	 *	Appends a submit button, generates and renders this instance of a form.
	 *	@return String
	 */
	public function thisForm(){
		$this->_fields->submit = new Zend_Form_Element_Submit('submit', array('label'=>'Submit', 'value'=>'Submit'));
		$this->setElements((array)$this->_fields);
		$this->setElementDecorators(array('ViewHelper', new Kizano_Forms_Decorator(array('tag'=>'div'))));
		return $this->render($this->view);
	}

	/**
	 *	Overrides the parent rendering function and renders the form for display
	 *	@return String
	 */
	public function render(Zend_View_Interface $view = null){
		$attribs = null;
		foreach($this->_attribs as $key => $attrib){
			$attrib = htmlEntities($attrib, ENT_QUOTES, 'utf-8');
			$attribs .= (empty($attribs)? null: chr(32))."$key='$attrib'";
		}
		$result = "\n\t\t\t\t\t<form method='{$this->getMethod()}'$attribs>\n";
		foreach($this->_elements as $element){
			$result .= $element->render($view);
		}
		return "$result\n\t\t\t\t\t</form>\n\t\t\t\t";
	}
}

