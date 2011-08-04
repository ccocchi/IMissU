<?php

class Model_DbTable_User extends Lib_Model_CacheAbstract {
	protected $_name = 'USER';
	protected $_primary = 'user_id';
	protected $_dependentTables = array(
		'Model_DbTable_Comment', 
		'Model_DbTable_Dedicace', 
		'Model_DbTable_Favorite',
		'Model_DbTable_Participe',
		'Model_DbTable_Photo',
		'Model_DbTable_Receive',
		'Model_DbTable_Thread',
		'Model_DbTable_Visit',
		'Model_DbTable_Vote'
	);
	
	public function findById($userId) {
		$select = $this->select()
					->where('user_id = ?', $userId);
		return $this->fetchRow($select);
	}
		
	public function findByFbId($fbUserId) {
		$select = $this->select()
					->where('fbid = ?', $fbUserId);
		return $this->fetchRow($select);
	}
	
	public function findByName($nickname) {
		$select = $this->select()	
					->where('nickname = ?', $nickname);
		return $this->fetchRow($select);
	}
	
	public function findAccueilPhotos(){
		$select = $this->select()
			->from(array('u' => 'USER'))
			->where('u.active = true')
			->where('u.miniature_id IS NOT NULL')
			->order('u.vote DESC')
			->limit(300);

		return $this->fetchAll($select);
	}
	
		
	public function changeProfilePhoto($user_id, $pic_id){
		$where = $this->getAdapter()->quoteInto('user_id = ?', $user_id);
		$this->update(array ('miniature_id' => $pic_id), $where);
	}
	
	public function findTopSixForContest($contestId) {
		$select = $this->select()
			->setIntegrityCheck(false)
			->from(array('u' => 'USER'))
			->join(array('p' => 'participe'), 'p.user_id = u.user_id', array('user_id', 'vote'))
			->where('p.contest_id = ?', $contestId)
			->where('u.active = true')
			->where('u.miniature_id IS NOT NULL')
			->order('p.vote DESC')
			->limit(6, 0);

		return $this->fetchAll($select);
	}

	public function findTopThreeForContest($contestId) {
		$select = $this->select()
			->setIntegrityCheck(false)
			->from(array('u' => 'USER'))
			->join(array('p' => 'participe'), 'p.user_id = u.user_id', array('user_id', 'vote'))
			->where('p.contest_id = ?', $contestId)
			->where('u.active = true')
			->where('u.miniature_id IS NOT NULL')
			->order('p.vote DESC')
			->limit(3, 0);

		return $this->fetchAll($select);
	}
	
	public function findUsersForContest($contestId) {
		$select = $this->select()
			->setIntegrityCheck(false)
			->from(array('u' => 'USER'))
			->join(array('p' => 'participe'), 'p.user_id = u.user_id', array('user_id', 'vote'))
			->where('p.contest_id = ?', $contestId)
			->where('u.active = true')
			->order('p.vote DESC');

		return $this->fetchAll($select);
	}
	
	public function findFavoritesForUser($userId) {
		$select = $this->select()
			->setIntegrityCheck(false)
			->from(array('u' => 'USER'))
			->join(array('f' => 'favorite'), 'f.use_user_id = u.user_id')
			->where('f.user_id = ?', $userId)
			->where('u.active = true');
			
		return $this->fetchAll($select);
	}

	public function findVotersForUser($userId) {
		$select = $this->select()
			->setIntegrityCheck(false)
			->from(array('u' => 'USER'), array('user_id', 'fbid', 'nickname', 'miniature_id'))
			->join(array('v' => 'vote'), 'v.user_id = u.user_id', array('SUM (v.points)'))
			->where('v.use_user_id = ?', $userId)
			->where('u.active = true')
			->group('u.fbid')
			->group('u.miniature_id')
			->group('u.user_id')
			->group('u.nickname');
			
		return $this->fetchAll($select);
	}
	
	public function findRandomsForUser($user = 0) {
		$select = $this->select()
			->from(array('u' => 'USER'), array('nickname', 'fbid', 'miniature_id'))
			->where('u.active = true')
			->where('u.miniature_id IS NOT NULL')
			->order('RANDOM()');
		
		return $this->fetchAll($select);
	}
	
	public function hasEnoughPoints($userId, $value) {
		$res = $this->execProc('has_enough_points', array($userId, $value));
		
		return $res['has_enough_points'];
	}
	
	public function findTopThreeMale() {
		$select = $this->select()
			->where('sex = true')
			->where('active = true')
			->where('miniature_id IS NOT NULL')
			->order('vote DESC')
			->limit(3, 0);

		return $this->fetchAll($select);
	}
	
	public function findTopThreeFemale() {
		$select = $this->select()
			->where('sex = false')
			->where('active = true')
			->where('miniature_id IS NOT NULL')
			->order('vote DESC')
			->limit(3, 0);

		return $this->fetchAll($select);
	}
	
	public function selectFindAllFemale() {
		$select = $this->select()
			->where('sex = false')
			->where('active = true')
			->where('miniature_id IS NOT NULL')
			->order('vote DESC');

		//return $this->fetchAll($select);
		return $select;
	}
	
	public function selectFindAllMale() {
		$select = $this->select()
			->where('sex = true')
			->where('active = true')
			->where('miniature_id IS NOT NULL')
			->order('vote DESC');
		//return $this->fetchAll($select);
		return $select;
	}	
	
	public function deleteUser ($id) {
		$where = $this->getAdapter()->quoteInto('user_id = ?', $id);
		$this->update(array ('active' => 'false'), $where);
	}

	public function activeUser ($id) {
		$where = $this->getAdapter()->quoteInto('user_id = ?', $id);
		$this->update(array ('active' => 'true'), $where);
	}
	
	public function addPoints($fbid, $points)
	{
		$select = $this->update(array('points' => new Zend_Db_Expr('points + '. $points)), "fbid = $fbid");
	}
	
	
	public function createnewVip ($fbid)
	{
		$select = $this->update(array(
			'end_vip'=> new Zend_Db_Expr('current_date + 31'), 'is_vip' => true
			), "fbid = $fbid");
	}
	
	public function updateVip ($fbid)
	{
		$select = $this->update(array(
			'end_vip'=> new Zend_Db_Expr('end_vip + 31'), 'is_vip' => true
			), "fbid = $fbid");
	}
}