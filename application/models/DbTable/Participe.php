<?php

class Model_DbTable_Participe extends Lib_Model_CacheAbstract {
	protected $_name = 'participe';
	protected $_primary = array('user_id', 'contest_id');
	protected $_referenceMap = array (
		'contestant' => array (
			'columns'		=> 'user_id',
			'refTableClass'	=> 'Model_DbTable_User',
			'refColumns'	=> 'user_id'
		),
		'contest' => array (
			'columns'		=> 'contest_id',
			'refTableClass'	=> 'Model_DbTable_Contest',
			'refColumns'	=> 'contest_id'
		)
	);
	
	public function voteFor ($contestId, $userId)
	{	
		$select = $this->update(array(
			'vote'=> new Zend_Db_Expr('1 + vote')
			), "contest_id = $contestId AND user_id = $userId");
	}

	public function isSubscribe ($contestId, $userId)
	{
		$select = $this->select()
			->from(array('p' => 'participe'))
			->where('p.contest_id = ?', $contestId)
			->where('p.user_id = ?', $userId);

		if (count ($this->fetchAll($select)) == 0)
			return false;
		return true;
	}
	
	public function participateTo($userId)
	{
		$select = $this->select()
			->from(array('p' => 'participe'), array('contest_id'))
			->where('p.user_id = ?', $userId);

		return $this->fetchAll($select);
	}
}