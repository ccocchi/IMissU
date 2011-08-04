<?php

class Model_DbTable_Contest extends Lib_Model_CacheAbstract {
	protected $_name = 'contest';
	protected $_primary = 'contest_id';
	protected $_dependentTables = array(
		'Model_DbTable_Participe'
	); 
	
	public function findCurrentContests() {
		$select = $this->select()
					->where('date_begin < now() AND date_end > now()');
		return $this->fetchAll($select);
	}
	
	
//	public function findAll () {
//		$select = $this->select();
//		return $this->fetchAll($select);
//	}
}