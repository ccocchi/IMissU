<?php

require_once 'FacebookController.php';

class ClassementController extends FacebookController {
	protected $_model;
	protected $_self;
	
	public function init() {
		parent::init();
		$this->requireLogin();
		$this->_model = new Model_DbTable_Contest();
		$userTable = new Model_DbTable_User();
		$this->_self = $userTable->enableCache($this->fbUserId)->findByFbId($this->fbUserId);
		$this->view->whoami = "ranking";
	}
	
	public function indexAction() {
		if (!$this->_helper->acl->isAllowed('classement', 'index')) {
			throw new Exception_NotSubscribed();
		}
		$contests = $this->_model->findCurrentContests();
		$participants = array();
		$userTable = new Model_DbTable_User();
		
		foreach ($contests as $contest) {
			$participants[$contest->contest_id] = $userTable->findTopThreeForContest($contest->contest_id);
		}
		
		$topThreeGeneralMale = $userTable->findTopThreeMale();
		$topThreeGeneralFemale = $userTable->findTopThreeFemale();
		
		$archiveTable = new Model_DbTable_Archive();
		$archives = $archiveTable->selectGetAllArchive();
		
		$this->view->contests = $contests;
		$this->view->participants = $participants;
		$this->view->topMale = $topThreeGeneralMale;
		$this->view->topFemale = $topThreeGeneralFemale;
		$this->view->archives = $archives;
	}
	
	public function browseAction() {
		if (!$this->_helper->acl->isAllowed('classement', 'index')) {
			throw new Exception_NotSubscribed();
		}
	
		$id = intval($this->_getParam('id'));
		$userTable = new Model_DbTable_User();
		
		$contest = $this->_model->find($id)->current();
		if (!$contest) {
			throw new Exception_PageNotFound();
		}
		$participants = $userTable->findUsersForContest($id);
		
		$this->view->contest = $contest;
		$this->view->participants = $participants;
	}

	public function browsegeneralAction() {
		if (!$this->_helper->acl->isAllowed('classement', 'browse')) {
			throw new Exception_NotSubscribed();
		}
		$gender = $this->_getParam('gender');
		$page = $this->_getParam('page', 1);
		
		$userTable = new Model_DbTable_User();
		if ($gender == "male")
			$select = $userTable->selectFindAllMale();
		elseif ($gender == "female")
			$select = $userTable->selectFindAllFemale();
		else
			throw new Exception_PageNotFound();

		$adapter = new Lib_Paginator_Adapter_CacheDbTableSelect($select);
		$p = new Zend_Paginator($adapter);
		$p->setItemCountPerPage(10);
		$p->setCurrentPageNumber($page);

		$this->view->paginator = $p;
		$this->view->type = $gender;
		$this->view->key = Lib_MCrypt::encrypt(Lib_MCrypt::$_seed . '_' . $this->fbUserId . '_' . Lib_MCrypt::$_seed);
	}
}