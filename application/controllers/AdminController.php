<?php
require_once 'FacebookController.php';

class AdminController extends FacebookController
{
	
	public function init() {
		parent::init();
		$this->requireLogin();
	}
	
	public function preDispatch() {
		if (!$this->_helper->acl->isAllowed('admin')) {
			throw new Exception_NoPrivileges();
		}
	}
	

	public function indexAction ()
	{
	}
	
	public function listuserAction ()
	{
		$userTable = new Model_DbTable_User ();
		$users = $userTable->fetchAll();
		
		$this->view->users = $users;
	}
	
	public function delAction ()
	{
		$id = $this->_getParam('id');
		$userTable = new Model_DbTable_User();
		$userTable->deleteUser($id);
		$this->_redirect('admin/listUser');
	}
	
	public function activAction ()
	{
		$id = $this->_getParam('id');
		$userTable = new Model_DbTable_User();
		$userTable->activUser($id);
		$this->_redirect('admin/listUser');
	}
	
	
	public function addcontestAction ()
	{
		$form = new Form_Contest();
		//$form->populate($this->_self->toArray());
		$this->view->form = $form;
		if ($this->_request->isPost()) {
			$formData = $this->_request->getPost();
			if ($form->isValid($formData)) {
				unset($formData['submit']);
				$contestTable = new Model_DbTable_Contest();
				$contestTable->insert($formData);
				
				mkdir(CONTEST_IMAGE_DIR_PATH.$contestTable->getAdapter()->lastInsertId("contest", "contest_id"), 0777);
				chmod(CONTEST_IMAGE_DIR_PATH.$contestTable->getAdapter()->lastInsertId("contest", "contest_id"), 0777);
				mkdir(CONTEST_THUMB_DIR_PATH.$contestTable->getAdapter()->lastInsertId("contest", "contest_id"), 0777);				
				chmod(CONTEST_THUMB_DIR_PATH.$contestTable->getAdapter()->lastInsertId("contest", "contest_id"), 0777);				
				
				$this->_redirect('admin/listContest');				
			} else {
				$form->populate($formData);
			}
		}
	}
	
	public function listcontestAction ()
	{
		$contestTable = new Model_DbTable_Contest();
		$contests = $contestTable->fetchAll();
		
//		var_dump ($contests->toArray ()); die;
		$this->view->contests = $contests;
	}

	public function adduserAction ()
	{
		$form = new Form_UserAdmin();
		//$form->populate($this->_self->toArray());
		$this->view->form = $form;
		if ($this->_request->isPost()) {
			$formData = $this->_request->getPost();
			if ($form->isValid($formData)) {
				unset($formData['submit']);
				$userTable = new Model_DbTable_User();
				$userTable->insert($formData);
				$this->_redirect('admin/deleteUser');
			} else {
				$form->populate($formData);
			}
		}
	}
	
}