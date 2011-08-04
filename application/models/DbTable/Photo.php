<?php

class Model_DbTable_Photo extends Lib_Model_CacheAbstract {
	protected $_name = 'photo';
	protected $_primary = 'photo_id';
	protected $_referenceMap = array (
		'owner' => array (
			'columns'		=> 'user_id',
			'refTableClass'	=> 'Model_DbTable_User',
			'refColumns'	=> 'user_id'
		)
	);
	
	public function findPhotosForUser($userId) {
		$select = $this->select()
			->where('user_id = ?', $userId)
			->order('photo_id DESC');
			
		return $this->fetchAll($select);
	}
	
	public function findPhotoByIdAndUser($photoId, $userId = null) {
		$select = $this->select()
			->where('photo_id = ?', $photoId);
		
		if ($userId) {
			$select = $select->where('user_id = ?', $userId);	
		}

		return $this->fetchRow($select);
	}
	
	public function moderate ($idPhoto)
	{
		$where = $this->getAdapter()->quoteInto('photo_id = ?', $idPhoto);
		$this->update(array ('validate' => 'false'), $where);
	}
	
	public function validate ($idPhoto)
	{
		$where = $this->getAdapter()->quoteInto('photo_id = ?', $idPhoto);
		$this->update(array ('validate' => 'true'), $where);
	}
	
	public function del ($idPhoto)
	{
		$userTable = new Model_DbTable_User();
		$where = $userTable->getAdapter()->quoteInto('miniature_id = ?', $idPhoto);
		$userTable->update(array ('miniature_id' => NULL), $where);
		$where = $this->getAdapter()->quoteInto('photo_id = ?', $idPhoto);
		$this->delete($where);
	}
}