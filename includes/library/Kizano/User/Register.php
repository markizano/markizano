<?php

require_once 'modules/user/models/User.php';

/**
 *	
 *	
 *	
 */
class Kizano_User_Register extends Kizano_User{

	/**
	 *	Attempts to register a user based on submitted credentials.
	 *	@param	form	Zend_Form							The form to use to validate and implement
	 *	@param	request	Zend_Controller_Request_Abstract	The request object
	 *	@return void
	 */
	public function registerUser(Zend_Form $form, Zend_Controller_Request_Abstract $request){
		$valid = $form->isValid($request->getPost());
		var_dump($valid);die;
		if($valid){
			foreach($this->formatVars($request->getPost()) as $name => $user){
				if($name == 'password') continue;
				$this[$name] = $user;
			}
			$user = $request->getPost();
			$this['salt'] = Kizano_Strings::strRandHex(8);
			$this['password'] = Kizano_Strings::hashPass($user['password'], $this['salt']);
			$this->save();
			return $this->getData();
		}
	}

	/**
	 *	Formats the given variables so that we are given only what's necessary
	 *	@notes	We use a whitelist type of request for the data instead of blindly
	 *			adding variables that could be injected into the application. 
	 *	@param	params		Array		The parameters to format/evaluate
	 *	@return	array
	 */
	public function formatVars(array $params){
		return array(
			'username' => $params['username'],
			'email' => $params['eMail'],
			'type' => $params['User_Type'],
			'name_first' => $params['Name_First'],
			'name_last' => $params['Name_Last'],
			'address_street_line1' => $params['Address_Street_Line1'],
			'address_street_line2' => $params['Address_Street_Line2'],
			'address_city' => $params['Address_City'],
			'address_state' => $params['Address_State'],
			'address_country' => $params['Country'],
			'phone' => $params['Phone'],
			'type' => substr($params['User_Type'], 0, 1),
		);
	}
}

