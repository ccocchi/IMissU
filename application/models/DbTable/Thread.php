<?php

class Model_DbTable_Thread extends Lib_Model_CacheAbstract {
	protected $_name = 'thread';
	protected $_primary = 'thread_id';
	protected $_referenceMap = array(
		'creator' => array (
			'columns'		=> 'user_id',
			'refTableClass'	=> 'Model_DbTable_User',
			'refColumns'	=> 'user_id'
		),
		'' => array (
			'columns'		=> 'use_user_id',
			'refTableClass'	=> 'Model_DbTable_User',
			'refColumns'	=> 'user_id'
		)
	);
	protected $_dependentTables = array(
		'Model_DbTable_Message'
	);
	
	public function findThreadsForAndByUser($userId) {
		$select = $this->select()
			->setIntegrityCheck(false)
			->from(array('t' => 'thread'), array('thread_id', 'subject', 'last_message'))
			->join(array('tu' => 'thread_user'), 't.thread_id = tu.thread_id', array('read'))
			//->join(array('m' => 'message'), 'm.thread_id = t.thread_id', 'count(m.message_id) as count')
			->join(array('u' => 'USER'), 't.user_id = u.user_id', 'nickname')
			->join(array('u1' => 'USER'), 't.use_user_id = u1.user_id','nickname AS to')
			->where('tu.user_id = ?', $userId)
			->where('tu.deleted = false')
			//->group(array('t.thread_id', 't.subject', 't.last_message', 'tu.read'))
			->order('t.last_message DESC');
		
		return $this->fetchAll($select);
	}
	
	public function setRead($threadId, $userId) {
		$this->getAdapter()->update('thread_user', array(
			'read' => 'true'
			), "thread_id = $threadId AND user_id = $userId");
	}
	
	public function setDeleted($threadId, $userId) {
		$this->getAdapter()->update('thread_user', array(
			'deleted' => 'true'
			), "thread_id = $threadId AND user_id = $userId");
	}
}