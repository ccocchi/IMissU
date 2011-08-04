<?php

class Model_DbTable_Vote extends Lib_Model_CacheAbstract {
	protected $_name = 'vote';
	protected $_primary = 'vote_id';
	protected $_referenceMap = array(
		'voter' => array (
			'columns'		=> 'user_id',
			'refTableClass'	=> 'Model_DbTable_User',
			'refColumns'	=> 'user_id'
		),
		'target' => array (
			'columns'		=> 'use_user_id',
			'refTableClass'	=> 'Model_DbTable_User',
			'refColumns'	=> 'user_id'
		)
	);
	
	public function findTopThree() {
		$select = $this->select()
			->setIntegrityCheck(false)
			->from(array('v' => 'vote'), array('SUM (v.points)', 'v.user_id'))
			->where('date_part (\'month\', v.date) = date_part (\'month\', current_date)')
			->group('v.user_id')
			->order('SUM (v.points) DESC')
			->limit(3, 0);

		return $this->fetchAll($select);
	}
	public function findClassement() {
		$select = $this->select()
			->setIntegrityCheck(false)
			->from(array('v' => 'vote'), array('SUM (v.points)', 'v.user_id'))
			->where('date_part (\'month\', v.date) = date_part (\'month\', current_date)')
			->group('v.user_id')
			->order('SUM (v.points) DESC');

		return $this->fetchAll($select);
	}
	
}