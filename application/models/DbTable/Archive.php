<?php

class Model_DbTable_Archive extends Lib_Model_CacheAbstract {
	protected $_name = 'archive';
	protected $_primary = 'archive_id';
	protected $_referenceMap = array (
		'contestant' => array (
			'columns'		=> 'user_id',
			'refTableClass'	=> 'Model_DbTable_User',
			'refColumns'	=> 'user_id'
		)
	);
	
	public function findTopThreeMale($year, $month) {
		$select = $this->select()
			->setIntegrityCheck(false)
			->from(array('a' => 'archive'), array('vote'))
			->join(array('u' => 'USER'), 'a.user_id = u.user_id', array('nickname', 'miniature_id', 'fbid'))
			->where('month = ?', $month)
			->where('year = ?', $year)
			->where('u.sex = true')
			->order('vote DESC')
			->limit(3, 0);
			
		return $this->fetchAll($select);
	}
	
	public function findTopThreeFemale($year, $month) {
		$select = $this->select()
			->setIntegrityCheck(false)
			->from(array('a' => 'archive'), array('vote'))
			->join(array('u' => 'USER'), 'a.user_id = u.user_id', array('nickname', 'miniature_id', 'fbid'))
			->where('month = ?', $month)
			->where('year = ?', $year)
			->where('u.sex = false')
			->order('vote DESC')
			->limit(3, 0);
			
		return $this->fetchAll($select);
	}
	
		public function selectFindAllFemale($year, $month) {
		$select = $this->select()
			->setIntegrityCheck(false)
			->from(array('u' => 'USER'))
			->join(array('a' => 'archive'), 'a.user_id = u.user_id')
			->where('month = ?', $month)
			->where('year = ?', $year)
			->where('sex = false')
			->where('active = true')
			->order('a.vote DESC');

		//return $this->fetchAll($select);
		return $select;
	}
	
		public function selectFindAllMale($year, $month) {
		$select = $this->select()
			->setIntegrityCheck(false)
			->from(array('u' => 'USER'))
			->join(array('a' => 'archive'), 'a.user_id = u.user_id')
			->where('month = ?', $month)
			->where('year = ?', $year)
			->where('sex = true')
			->where('active = true')
			->order('a.vote DESC');

		//return $this->fetchAll($select);
		return $select;
	}
	
	public function selectGetAllArchive(){
		$select = $this->select()
			->from (array('a' => 'archive'), array ('month', 'year'))
			->group(array('year', 'month'))
			->order(array('year DESC', 'month'));
		
		return $this->fetchAll($select);
	}
	
}