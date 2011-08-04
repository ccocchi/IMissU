<?php

class Model_DbTable_Dedicace extends Lib_Model_CacheAbstract {
	protected $_name = 'dedicace';
	protected $_primary = 'dedicace_id';
	protected $_referenceMap = array (
		'author' => array (
			'columns'		=> 'user_id',
			'refTableClass'	=> 'Model_DbTable_User',
			'refColumns'	=> 'user_id'
		)
	);
	
	public function selectGetCurDedicace(){
		$select = $this->select()
			->setIntegrityCheck(false)
			->from (array ("d" => "dedicace"),array('content'))
			->join (array ("u" => "USER"), 'd.user_id = u.user_id', array ('nickname'))
			->where ('d.date_end > ?', date('Y-m-j'));
			
		return $this->fetchAll($select);
	}
	
	public function selectFindCommentsFor($userId) {
		$select = $this->select()
			->setIntegrityCheck(false)
			->from (array("c" => "comment"), array('comment_id', 'message', 'date'))
			->join(array("u" => "USER"), 'u.user_id = c.user_id', array('nickname'))
			->where('c.use_user_id = ?', $userId)
			->order('comment_id DESC');
			
		return $select;
	}
	
	public function getDediUSer($userId)
	{
		$select = $this->select ()
			->from (array ("d" => "dedicace"),array('content'))
			->where ('d.date_end > ?', date('Y-m-j'))
			->where ('d.user_id = ?', $userId);
		return $this->fetchRow($select);
	}
	
}