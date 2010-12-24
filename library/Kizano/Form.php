<?php
/**
 *	@Name: ~/library/Kizano/Form.php
 *	@Depends: ~/library/Zend/Form.php
 *	@Description: Main Form class
 *	@Notes: Edit with Care
 *
 *	Kizano: ZF-Friendly library extensions.
 *	@CopyRight: (c) 2010 markizano Draconus <markizano@markizano.net>
 */

class Kizano_Form extends Zend_Form{

	# Holds the name of the current form
	protected $_formName;

	# Holds the fields to render in the form
	protected $_fields = array();

	# Default configuration to inject into the form
	protected $_defaults = array();

	# @Zend_View instance to help with the rendering
	public $view;

	/**
	 *	Generates a new instance of this formset
	 *	@param	Zend_Config		options		(Optional) Zend_Form Configuration options
	 *	@return void
	 */
	public function __construct($options = null)
	{

        if (is_array($options)) {
            $this->setOptions($options);
        } elseif ($options instanceof Zend_Config) {
            $this->setConfig($options);
        }

		$this->view = Zend_Registry::getInstance()->get('view');
		$this->setName('Default_Form');
		$this->setAction('');
		$this->setDisableLoadDefaultDecorators(true);
		$this->clearDecorators();

        // Extensions...
        $this->init();
	}

	/**
	 *	Gets a particular form of this extension.
	 *	@param	name		string		The name of the form extension to obtain.
	 *	@return	Kizano_Form
	 *	@throws	Kizano_Form_Exception
	 */
	public static function getForm($name) {
		if (!is_string($name)) {
			throw new Kizano_Form_Exception(sprintf(
				"%s::%s(): @param \$name: Expected(string); Received(%s).",
				__CLASS__,
				__FUNCTION__,
				getType($name)
			));
			return false;
		}
		$formName = 'Kizano_Form_'.ucWords($name);
		if (!class_exists($formName, false)) {
			Zend_Loader::loadClass($formName);
		}
		$form = new $formName;
		$form->$name();
		if (!$form) return false;
		return $form;
	}

	/**
	 *	Adds a form element to this form.
	 *	@param name		string				The name of the field to add.
	 *	@param type		string				The fully qualified classname of the field to add.
	 *	@param options	array|Zend_Config	Configuration options to add to the element.
	 *	@return Kizano_Form
	 */
	public function addField($name, $type, $options = array()) {
		if (!is_string($name)) {
			throw new Kizano_Form_Exception(sprintf(
				"%s::%s(): Param type of \$name; Expected (string), Received (%s)",
				__CLASS__,
				__FUNCTION__,
				get_type($name)
			));
			return false;
		}

		if (!is_string($type)) {
			throw new Kizano_Form_Exception(sprintf(
				"%s::%s(): Param type of \$type; Expected (string), Received (%s)",
				__CLASS__,
				__FUNCTION__,
				get_type($type)
			));
			return false;
		}

		if (!is_array($options) && !($options instanceof Zend_Config)) {
			throw new Kizano_Form_Exception(sprintf(
				"%s::%s(): Param type of \$options; Expected (array|Zend_Config), Received (%s)",
				__CLASS__,
				__FUNCTION__,
				get_type($options)
			));
			return false;
		}

		$this->_fields[$name] = new $type($name, $options);
		return $this;
	}

	/**
	 *	Gets a specified field from this form.
	 *	@param	name		The field name to obtain.
	 *	@return mixed		False if the field does not exist, Zend_Form_Element otherwise.
	 */
	public function getField($name) {
		if (!isset($this->_fields[$name])) {
			return false;
		}
		return $this->_fields[$name];
	}

	/**
	 *	Adds fields to the form.
	 *	@param fields		Array	The fields to add.
	 *	@return Kizano_Form
	 */
	public function addFields(array $fields) {
		$this->_fields = $fields;
		return $this;
	}

	/**
	 *	Gets all the fields in this form.
	 *	@return array
	 */
	public function getFields() {
		return $this->_fields;
	}

	/**
	 *	Clears the fields from this form.
	 *	@return Kizano_Form
	 */
	public function clearFields() {
		$this->_fields = array();
		return $this;
	}

	/**
	 *	Validates this form to ensure the user submitted the proper data.
	 *	@param params		Array	The information the user submitted to validate.
	 *	@return boolean
	 */
	public function isValid($params = array()) {
		$result = parent::isValid($params);
		foreach ($this->getFields() as $name => $field) {
			if (!isset($params[$name])) continue;
			if (!$field->isValid($params[$name])) {
				foreach ($field->getValidators() as $validator) {
					if (!$validator->isValid($params[$name])) {
						$field->setErrorMessages($validator->getMessages());
					}
				}
				$result = false;
			}
		}
		return (boolean)$result;
	}

	/**
	 *	Adds validators, filters, appends a submit button, generates and renders 
	 *		this instance of a form.
	 *	@return Kizano_Form
	 */
	public function finalizeForm() {
		# Add internal validators
		$this->_validate();
		# Add internal filters
		$this->_filter();

		# Add a submit button
		$this->addField(
			'submit',
			'Zend_Form_Element_Submit',
			array(
				'label'		=> 'Submit',
				'value'		=> 'Submit',
				'onclick'	=> 'this.disable = true',
			)
		);
		$this->setElements($this->getFields());
		$this->setElementDecorators(array('ViewHelper', new Kizano_Form_Decorator(array('tag'=>'div'))));
		return $this;
	}

	/**
	 *	Overrides the parent rendering function and renders the form for display.
	 *	@return string
	 */
	public function render(Zend_View_Interface $view = null) {
		$result = null;
		$attribs = array();
		foreach ($this->_attribs as $key => $attrib) {
			$attrib = htmlEntities($attrib, ENT_QUOTES, 'utf-8');
			$attribs[] .= "$key='$attrib'";
		}
		$attribs = join(chr(32), $attribs);

		$result .= "\n\t\t\t\t\t<form method='{$this->getMethod()}'$attribs>\n";
		if ($this->getErrorMessages()) {
			$result .= "\t\t\t<ul class='error'>\n\t\t\t\t<li>";
			$result .= join("</li>\n\t\t\t\t<li>", $this->getErrorMessages());
			$result .= "</li>\n\t\t\t</ul>\n";
		}
		foreach ($this->_elements as $element) {
			$result .= $element->render($view);
		}
		$result .= "\n\t\t\t\t\t</form>\n\t\t\t\t";
		return $result;
	}

	/**
	 *	Placeholder for adding filters.
	 *	@return void
	 */
	protected function _filter()
	{}

	/**
	 *	Placeholder for adding validators.
	 *	@return void
	 */
	protected function _validate() {
		foreach ($this->_fields as $name => $field) {
			if ($field->isRequired()) {
				$this->_fields[$name]->addValidator(new Zend_Validate_NotEmpty);
			}
		}
	}

	/**
	 *	Magic function to return the string representation of this form.
	 *	@return string
	 */
	public function __toString() {
		return $this->render($this->view);
	}
}

