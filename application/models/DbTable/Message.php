<?php

class Model_DbTable_Message extends Lib_Model_CacheAbstract {
	protected $_name = 'message';
	protected $_primary = 'message_id';
	protected $_referenceMap = array (
		'thread' => array (
			'columns'		=> 'thread_id',
			'refTableClass'	=> 'Model_DbTable_Thread',
			'refColumns'	=> 'thread_id'
		)
	);
	
	public function findMessagesByThread($thread_id) {
		$select = $this->select()
					->setIntegrityCheck(false)
					->from(array('m' => 'message'))
					->join(array('u' => 'USER'), 'u.user_id = m.user_id', array('nickname', 'miniature_id', 'fbid'))
					->where('m.thread_id = ?', $thread_id)
					->order('message_id');
		return $this->fetchAll($select);
	}
}