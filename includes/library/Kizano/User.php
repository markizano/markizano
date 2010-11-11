<?php

require_once 'modules/user/models/Base/User.php';

/**
 *	
 *	
 *	
 */
class Kizano_User extends User_Model_Base_User{
	
	/**
	 *	Gets all users from the DB
	 *	@return array
	 */
	public function getUsers(){
		return $this->getAll();
	}

	/**
	 *	Gets a single user from the DB based on the provided condition
	 *	@param slug	String	Get user by $slug. For example:
	 *							$this->getUserByUserName() == $this->getUserBy('UserName'...);
	 *	@param info	String	The condition by which to obtain the user.
	 *	@return array
	 */
	public function getUsersBy($slug, $info = null){
		return $this->getAllBy($slug, $info);
	}

	/**
	 *	Shortcut function to obtain users by ID
	 *	@param ID	The ID by which to obtain the user
	 *	@return array
	 */
	public function getUserById($ID){
		$_id = array_keys($this->identifier());
		return $this->getUserBy(Current($_id), $ID);
	}

	/**
	 *	Adds a user to the DB
	 *	@param	info	Array|Zend_Config		The user data to insert
	 *	@return void
	 */
	public function addUser($info){
		$this->_create($info);
	}

	/**
	 *	Updates a user's info in the DB.
	 *	@param	ID		Integer				The ID of the user to update.
	 *	@param	Info	Array|Zend_Config	The info to use to update the user.
	 */
	public function updateUser($ID, $info = array()){
		$this->_update($ID, $info);
	}

	/**
	 *	Removes a user from the DB
	 *	@param	ID	Integer		The ID of the user to remove.
	 *	@return void
	 */
	public function delUser($ID){
		$this->_remove($ID);
	}
}

