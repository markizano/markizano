<?php
/**
 *	@Name: ~/includes/modules/forms/decorator.module
 *	@Depends: ~/includes/library/Zend/Form.php
 *	@Description: Redecorates the contact form to suit the site's needs
 *	@Notes: Edit with care
 *
 *	Kizano: ZF-Friendly library extensions.
 *	@CopyRight: (c) 2010 markizano Draconus <markizano@markizano.net>
 */

class Kizano_Form_Decorator
	extends Zend_Form_Decorator_Abstract
		implements Zend_Form_Decorator_Marker_File_Interface
{
	public function buildLabel(){
		$element = $this->getElement();
		if(strIpos($element->id, 'submit') !== false) return null;
		$label = $element->getLabel();

		if($translator = $element->getTranslator()){
			$label = $translator->translate($label);
		}
		if($element->isRequired()){
			$label .= '<span class="req">*</span>';
		}
		return $label;
	}

	public function buildInput(){
		$element = $this->getElement();
		$helper	= $element->helper;
		return $element->getView()->$helper(
			$element->getName(),
			$element->getValue(),
			$element->getAttribs(),
			$element->options
		);
	}

	public function buildErrors(){
		$element	= $this->getElement();
		$messages = $element->getMessages();
		if(empty($messages)) return '';
		return "<div class='errors'>{$element->getView()->formErrors($messages)}</div>";
	}

	public function buildDescription(){
		$element = $this->getElement();
		$desc	= $element->getDescription();
		if(empty($desc)){
			return '';
		}
		return "<div class='description'>$desc</div>";
	}

	public function render($content){
		$element = $this->getElement();
		if(!$element instanceof Zend_Form_Element){
			return $content;
		}
		if($element->getView() === null){
			return $content;
		}
		$content	= trim($content);
		$label		= $this->buildLabel();
		$input		= $this->buildInput();
		$errors		= $this->buildErrors();
		$desc		= $this->buildDescription();
		if($element instanceof Zend_Form_Element_Multi)
			$select = ' _select';
		elseif($element instanceof Zend_Form_Element_Submit)
			$select = ' _submit';
		else
			$select = null;

		$result = "\t\t\t\t\t\t<div class='element$select'>$errors$label$content$desc</div>\n";
		return $result;
	}
}
