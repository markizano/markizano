<?php
/**
 *	@Name: ~/library/Kizano/Record.php
 *	@Date: 2010-10-19/00:20
 *	@Depends: ~/library/Doctrine/Record.php
 *	@Description: An extension of the doctrine library to provide some central core
 *				functions as opposed to creating the same junk in a class repeatedly.
 *	@Notes: All methods in this class assume the first primary identifier as defined
 *			by the configuration.
 *	
 *	Kizano: ZF-Friendly library extensions
 *	@CopyRight: (c) 2010 Markizano Draconus <markizano@markizano.net>
 */

/**
 *	Extends the doctrine record to provide some extensive getters and setters.
 *	@const	COLUMNS		The columns for the current table in the database
 *	@member	getQuery	Gets a suitable query to execute or specify more options
 *	@member	getAll		Gets contents of this table in the current DB
 *	@member	getAllBy	Gets a specified column according to a certain condition
 *	@member _create		Creates a new entry in the database
 *	@member	_update		Updates a record in the database
 *	@member	_remove		Removes an entry in the database
 */
class Kizano_Record extends Doctrine_Record{

	# The current columns of this table
	const COLUMNS = '*';

	/**
	 *	Gets a suitable query to execute or extend options.
	 *	@return Doctrine_Query
	 */
	public function getQuery(){
		if($this::COLUMNS == '*') trigger_error(sprintf("Select * is EVIL! ._.  Please review class `%s' and be sure to override %1\$s::COLUMNS", get_class($this)), E_USER_WARNING);
		return Doctrine_Query::create()
			->select($this::COLUMNS)
			->from(get_class($this).' me');
	}

	/**
	 *	Gets the contents of the current table in the database.
	 *	@return array
	 */
	public function getAll(){
		return $this->getQuery()
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	}

	/**
	 *	Gets all rows of a specified column based on a conditional clause.
	 *	@param	key		String|Array				The name of the column(s) to obtain
	 *	@param	what	String|Array|Zend_Config	The conditions by which we obtain the data.
	 *	@return	array
	 */
	public function getAllBy($key, $what = null){
		$q = $this->getQuery();
		if($what instanceof Zend_Config)
			$what = $what->toArray();
		if(is_array($key))
			$key = join(', ', $key);
		if(is_array($what) && Count($what)){
			foreach($what as $key => $val)
				$q->where("$key = ?", $val);
		}elseif(is_string($what)){
			$q->where("key = ?", $what);
		}
		$_id = array_keys($this->identifier());
		$id = Current($_id);
		return $q->orderBy("$id ASC")
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	}

	/**
	 *	Creates a new record and adds it to the DB based on the stuff given in $info
	 *	@param	info	Array|Zend_Config		The stuff to put in the DB
	 *	@return			$this
	 */
	protected function _create($info){
		if($info instanceof Zend_Config)
			$info = $info->toArray();
		if(!is_array($info))
			throw new Kizano_Exception(
				sprintf(
					'%s::%s(): Argument $info expected type array|Zend_Config, received `%s\'',
					__CLASS__,
					__FUNCTION__,
					get_type($info)
				)
			);
		foreach($info as $name => $value)
			$this[$name] = $value;
		return $this->save();
	}

	/**
	 *	Updates a record in the DB according to the info
	 *	@param	id		Int					The ID of the primary key in the DB to update
	 *	@param	infos	Array|Zend_Config	The info to update the DB
	 *	@return			$this;
	 */
	protected function _update($id, $infos){
		$update = $this->getTable()->find($id);
		if($infos instanceof Zend_Config)
			$infos = $infos->toArray();
		if(!is_array($infos))
			throw new Kizano_Exception(
				sprintf(
					'%s::%s(): Argument $info expected type array|Zend_Config, received `%s\'',
					__CLASS__,
					__FUNCTION__,
					get_type($infos)
				)
			);
		foreach($infos as $name => $value)
			$update[$name] = $value;
		return $update->save();
	}

	/**
	 *	Removes an entry from the DB
	 *	@param	id		Int					The ID of the primary key to remove
	 *	@return			$this
	 */
	protected function _remove($id){
		$_id = array_keys($this->identifier());
		$primary = Current($_id);
		return Doctrine_Query::create()
			->delete(get_class($this))
			->where("$primary = ?", $id)
			->execute();
	}
}

