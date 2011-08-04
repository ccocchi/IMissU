<?php

class Model_DbTable_Comment extends Lib_Model_CacheAbstract {
	protected $_name = "comment";
	protected $_primary = 'comment_id';
	protected $_referenceMap = array(
		'poster' => array (
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
	
	public function selectFindCommentsFor($userId) {
		$select = $this->select()
			->setIntegrityCheck(false)
			->from(array('c' => 'comment'), array('comment_id', 'message', 'date'))
			->join(array('u' => 'USER'), 'u.user_id = c.user_id', array('nickname', 'miniature_id', 'fbid'))
			->where('c.use_user_id = ?', $userId)
			->order('c.comment_id DESC');
			
		return $select;
	}
	
	public function moderate ($idComment)
	{
		$where = $this->getAdapter()->quoteInto('photo_id = ?', $idComment);
		$this->update(array ('validate' => 'false'), $where);
	}
	
	public function validate ($idComment)
	{
		$where = $this->getAdapter()->quoteInto('photo_id = ?', $idComment);
		$this->update(array ('validate' => 'true'), $where);
	}	
	
	public function deleteComment($idComment){
		$where = $this->getAdapter()->quoteInto('comment_id = ?', $idComment);
		$this->delete($where);
	}
}