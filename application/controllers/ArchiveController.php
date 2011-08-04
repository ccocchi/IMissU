<?php
require_once 'FacebookController.php';

class ArchiveController extends FacebookController
{
	protected $_year;
	protected $_month;
	
	public function init() {
		parent::init();
		$this->requireLogin();
		$this->_model = new Model_DbTable_Archive();
		$userTable = new Model_DbTable_User();
		$this->_self = $userTable->enableCache($this->fbUserId)->findByFbId($this->fbUserId);
		
		$this->_year = intval($this->_getParam('year'));
		$this->_month = intval($this->_getParam('month'));
		
		if (!$this->_year || !$this->_month) {
			throw new Exception_PageNotFound();
		}
	}
	
	public function preDispatch() {
		if (!$this->_helper->acl->isAllowed('archive')) {
			throw new Exception_NotSubscribed();
		}
	}
	
	public function indexAction() {
		$topThreeGeneralMale = $this->_model->findTopThreeMale($this->_year, $this->_month);
		$topThreeGeneralFemale = $this->_model->findTopThreeFemale($this->_year, $this->_month);
		
		$this->view->topMale = $topThreeGeneralMale;
		$this->view->topFemale = $topThreeGeneralFemale;
		$this->view->month = $this->_month;
		$this->view->year = $this->_year;
	}
	
	public function showAction() {
		$gender = $this->_getParam('gender');
		$page = $this->_getParam('page', 1);
		
		$archiveTable = new Model_DbTable_Archive();
		if ($gender == "male")
			$select = $archiveTable->selectFindAllMale($this->_year, $this->_month);
		elseif ($gender == "female")
			$select = $archiveTable->selectFindAllFemale($this->_year, $this->_month);
		else
			throw new Exception_PageNotFound();


		$adapter = new Lib_Paginator_Adapter_CacheDbTableSelect($select);
		$p = new Zend_Paginator($adapter);
		$p->setItemCountPerPage(10);
		$p->setCurrentPageNumber($page);

		$this->view->paginator = $p;
		$this->view->users = $top;
		$this->view->type = $gender;
		$this->view->key = Lib_MCrypt::encrypt(Lib_MCrypt::$_seed . '_' . $this->fbUserId . '_' . Lib_MCrypt::$_seed);
	}
}