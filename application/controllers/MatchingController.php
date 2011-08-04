<?php

require_once 'FacebookController.php';

class MatchingController extends FacebookController {
	protected $_model;
	protected $_self;
	//protected $_cache;
	
	public function init() {
		parent::init();
		$this->requireLogin();
		$this->_model = new Model_DbTable_User();
		$this->_self = $this->_model->enableCache($this->fbUserId)->findByFbId($this->fbUserId);
		$this->view->whoami = "next";
		//$this->_cache = Zend_Registry::get('cache');
		//$this->view->errorMessages = $this->_helper->FlashMessenger->getMessages('error');
	}

	public function preDispatch() {
		if (!$this->_helper->acl->isAllowed('matching')) {
			throw new Exception_NotSubscribed();
		}
	}
	
	
	public function randomAction() {
//		$users = $this->_model
//			->enableCache(
//				'random_' . $this->fbUserId,
//				array(),
//				900)
//			->findRandomsForUser($this->_self->user_id);
//		$offset = $this->_cache->load('offset_' . $this->fbUserId);
//		if ($offset === false) {
//			$offset = 	
//		}
//		if ($offset = $this_cache	)
		
		$nickname = $this->_getParam("nickname");
		
		if ($nickname == ""){
			$users = $this->_model->enableCache()->fetchAll("miniature_id IS NOT NULL");
			$offset = rand(0, $users->count() - 1);
			$current = $users->getRow($offset);
		}
		else
		{
			$userTable = new Model_DbTable_User();
			$current = $userTable->findByName($nickname);
		}
		
		//$dir = ($prefix != 'original' ? USER_THUMB_DIR_PATH : USER_IMAGE_DIR_PATH);
	//	$name = Lib_Namer::pictureName($fbId, $photoId, $extension, $prefix);		
				
		$pictureName = Lib_Namer::pictureName($current->fbid, $current->miniature_id);
		$dir = USER_IMAGE_DIR_PATH;
		$path = $this->view->baseUrl($dir . $pictureName);
		
		$hash = Lib_MCrypt::encrypt($current->user_id . '_' . Lib_MCrypt::$_seed . '_' . date('Y-m-d'));
		$username = $current->nickname;
		
		$this->view->picture = $path;
		$this->view->hash = $hash;
		$this->view->username = $username;
		$this->view->fbid = Lib_MCrypt::encrypt(Lib_MCrypt::$_seed . '_' . $this->fbUserId . '_' . Lib_MCrypt::$_seed);
		
		//$this->_save = $this->_model->fetchAll();
		/*$contests = $this->_model->findCurrentContests();
		$participants = array();
		$userTable = new Model_DbTable_User();
		
		foreach ($contests as $contest) {
			$participants[$contest->contest_id] = $userTable->findTopThreeForContest($contest->contest_id);
		}
		
		$this->view->contests = $contests;
		$this->view->participants = $participants;*/
	}
}
