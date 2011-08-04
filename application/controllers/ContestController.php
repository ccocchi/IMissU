<?php

require_once 'FacebookController.php';

class ContestController extends FacebookController {
	protected $_model;
	protected $_self;
	
	public function init() {
		parent::init();
		$this->requireLogin();
		$this->_model = new Model_DbTable_Contest();
		$userTable = new Model_DbTable_User();
		$this->_self = $userTable->enableCache($this->fbUserId)->findByFbId($this->fbUserId);
		$this->view->errorMessages = $this->_helper->FlashMessenger->getMessages('error');
		$this->view->whoami = "concours";
	}

	public function indexAction() {
		if (!$this->_helper->acl->isAllowed('contest', 'index')) {
			throw new Exception_NotSubscribed();
		}
		
		$contests = $this->_model->findCurrentContests();
		$pictures = array();
		$userTable = new Model_DbTable_User();
		$participateTable = new Model_DbTable_Participe(); 

		foreach ($contests as $contest) {
			$pictures[$contest->contest_id] = $userTable->findTopSixForContest($contest->contest_id);
		}
		
		$i_participate = $participateTable->participateTo($this->_self->user_id)->toArray();
		$i_participate_to = array();
		foreach ($i_participate as $contest)
		{
			$i_participate_to[$contest["contest_id"]] = true;
		}
		$this->view->i_participate = $i_participate_to;
		$this->view->contests = $contests;
		$this->view->pictures = $pictures;
		$this->view->meh = $this->_self->user_id;
	}
	
	public function subscribeAction() {
		if (!$this->_helper->acl->isAllowed('contest', 'actions')) {
			throw new Exception_NotSubscribed();
		}
		
		$id = intval($this->_getParam('id'));
		
		$contest = $this->_model->find($id)->current();
		if (!$contest) {
			throw new Exception_PageNotFound();
		}
		
		$form = new Form_Picture();
		
		if ($this->_request->isPost()) {
			$formData = $this->_request->getPost();
			if ($form->isValid($formData)) {
				// Vérification de l'upload
				$upload = new Zend_File_Transfer_Adapter_Http();
				//$upload->addValidator('MimeType', false, array('image/jpeg', 'image/gif', 'image/png'));
				//$upload->addValidator('FilesSize', false, '500kB');
				
				if ($upload->isValid()) {
					
					$return = $this->_model->execProc('subscribe_to_contest', array(
						$contest['contest_id'],
						$this->_self->user_id,
						10
					));
					$this->view->money = $this->_self->points + 10;
					if (!$return['subscribe_to_contest']) {
						$flashMessenger = $this->_helper->getHelper('FlashMessenger');
						$flashMessenger->addMessage('Vous etes déjà inscrit à ce concours', 'error');
						$this->_redirect('contest/');
						return;
					}

					// Ajout de la photo dans la BDD
					$photoTable = new Model_DbTable_Photo();
					$id = $photoTable->insert(array(
						'user_id' => $this->_self->user_id,
						'validate' => 'false',
						'date' => 'now()'
					));
					
					$info = $upload->getFileInfo('file');
					$tmp_name = $info['file']['tmp_name'];
					
					// Original for contest
					$thumb = PhpThumb_PhpThumbFactory::create($tmp_name);
					$thumb->resize(640,640);
					$name = Lib_Namer::contestPictureName($this->_self->user_id);
					$thumb->save(CONTEST_IMAGE_DIR_PATH . $contest['contest_id'] . '/' . $name, 'jpg');
					
					// Small image for contest
					$thumb = PhpThumb_PhpThumbFactory::create($tmp_name);
					$thumb->resize(140, 140);
					$name = $name = Lib_Namer::contestPictureName($this->_self->user_id, 'jpg', 'small');
					$thumb->save(CONTEST_THUMB_DIR_PATH . $contest['contest_id'] . '/' . $name, 'jpg');
					
					// Mini image for contest
					$thumb = PhpThumb_PhpThumbFactory::create($tmp_name);
					$thumb->adaptiveResize(60,60);
					$name = Lib_Namer::contestPictureName($this->_self->user_id, 'jpg', 'mini');
					$thumb->save(CONTEST_THUMB_DIR_PATH . $contest['contest_id'] . '/' . $name, 'jpg');
					
					// Original for profile
					$thumb = PhpThumb_PhpThumbFactory::create($tmp_name);
					$thumb->resize(640, 640);
					$name = Lib_Namer::pictureName($this->fbUserId, $id, 'jpg');
					$thumb->save(USER_IMAGE_DIR_PATH . $name, 'jpg');
					
					// Mini image
					$thumb = PhpThumb_PhpThumbFactory::create($tmp_name);
					$thumb->adaptiveResize(60, 60);
					$name = Lib_Namer::pictureName($this->fbUserId, $id, 'jpg', 'mini');
					$thumb->save(USER_THUMB_DIR_PATH . $name, 'jpg');
					
					// Profile image
					$thumb = PhpThumb_PhpThumbFactory::create($tmp_name);
					$thumb->resize(200);
					$name = Lib_Namer::pictureName($this->fbUserId, $id, 'jpg', 'profile');
					$thumb->save(USER_THUMB_DIR_PATH . $name, 'jpg');
					
					// Classement image
					$thumb = PhpThumb_PhpThumbFactory::create($tmp_name);
					$thumb->adaptiveResize(160, 200);
					$name = Lib_Namer::pictureName($this->fbUserId, $id, 'jpg', 'classement');
					$thumb->save(USER_THUMB_DIR_PATH . $name, 'jpg');
					
					// Small image
					$thumb = PhpThumb_PhpThumbFactory::create($tmp_name);
					$thumb->resize(140, 140);
					$name = $name = Lib_Namer::pictureName($this->fbUserId, $id, 'jpg', 'small');
					$thumb->save(USER_THUMB_DIR_PATH . $name, 'jpg');
					
					$this->_redirect('contest/');
				} else {
					$form->populate($formData);
				}
			} else {
				$form->populate($formData);
			}
		}
		
		$photoTable = new Model_DbTable_Photo();
		$pictures = $photoTable->enableCache()->fetchAll($photoTable->select()->where('user_id = ?', $this->_self->user_id));
		
		$this->view->form = $form;
		$this->view->contest = $contest; 
		$this->view->pictures = $pictures;
		$this->view->user = $this->_self;
	}
	
	public function usephotoAction() {
		if (!$this->_helper->acl->isAllowed('contest', 'actions')) {
			throw new Exception_NotSubscribed();
		}
		
		$id = intval($this->_getParam('id'));
		$contestId = intval($this->_getParam('cid'));
		
		$return = $this->_model->execProc('subscribe_to_contest', array(
			$contestId,
			$this->_self->user_id,
			10
		));
		if (!$return['subscribe_to_contest']) {
			$flashMessenger = $this->_helper->getHelper('FlashMessenger');
			$flashMessenger->addMessage('Vous etes déjà inscrit à ce concours', 'error');
			$this->_redirect('contest/');
			return;
		}
		
		$srcname = Lib_Namer::pictureName($this->fbUserId, $id);
		// Original for contest
		$thumb = PhpThumb_PhpThumbFactory::create(USER_IMAGE_DIR_PATH . $srcname);
		$thumb->resize(640,640);
		$name = Lib_Namer::contestPictureName($this->_self->user_id);
		$thumb->save(CONTEST_IMAGE_DIR_PATH . $contestId . '/' . $name, 'jpg');
		
		// Mini image for contest
		$thumb = PhpThumb_PhpThumbFactory::create(USER_IMAGE_DIR_PATH . $srcname);
		$thumb->adaptiveResize(60,60);
		$name = Lib_Namer::contestPictureName($this->_self->user_id, 'jpg', 'mini');
		$thumb->save(CONTEST_THUMB_DIR_PATH . $contestId . '/' . $name, 'jpg');
		
		// Small image for contest		
		$thumb = PhpThumb_PhpThumbFactory::create(USER_IMAGE_DIR_PATH . $srcname);
		$thumb->resize(140,140);
		$name = Lib_Namer::contestPictureName($this->_self->user_id, 'jpg', 'small');
		$thumb->save(CONTEST_THUMB_DIR_PATH . $contestId . '/' . $name, 'jpg');
		
		$this->view->money = $this->_self->points + 10;
		$this->_redirect('contest/');
	}
	
	public function unsubscribeAction() {
		if (!$this->_helper->acl->isAllowed('contest', 'actions')) {
			throw new Exception_NotSubscribed();
		}
		
		$id = intval($this->_getParam('id'));
		
		// Delete from table
		$participeTable = new Model_DbTable_Participe();
		if (!$participeTable->isSubscribe($id, $this->_self->user_id)) {
			$flashMessenger = $this->_helper->getHelper('FlashMessenger');
			$flashMessenger->addMessage('Vous n\'etes pas inscrit à ce concours', 'error');
			$this->_redirect('contest/');
			return;
		}
		$participeTable->delete("user_id = '" . $this->_self->user_id . "' AND contest_id = '" . intval($id) . "'");
		
		// Delete file associated to contestant
		$name = Lib_Namer::contestPictureName($this->_self->user_id);
		unlink(CONTEST_IMAGE_DIR_PATH . $id . '/' . $name);
		$name = Lib_Namer::contestPictureName($this->_self->user_id, 'jpg', 'small');
		unlink(CONTEST_THUMB_DIR_PATH . $id . '/' . $name);
		$name = Lib_Namer::contestPictureName($this->_self->user_id, 'jpg', 'mini');
		unlink(CONTEST_THUMB_DIR_PATH . $id . '/' . $name);
				
		
		$this->_redirect('contest/');
		
	}
	
	public function showAction() {
		if (!$this->_helper->acl->isAllowed('contest', 'actions')) {
			throw new Exception_NotSubscribed();
		}
		
		$id = intval($this->_getParam('id'));
		
		$contest = $this->_model->find($id)->current();
		if (!$contest) {
			throw new Exception_PageNotFound();
		}
		
		$userTable = new Model_DbTable_User();
		$contestants = $userTable->enableCache()->findUsersForContest($contest->contest_id);
		
		$participeTable = new Model_DbTable_Participe();
		$this->view->isSubscribe = $participeTable->isSubscribe($contest->contest_id, $this->_self->user_id);
		
		$this->view->key = Lib_MCrypt::encrypt(Lib_MCrypt::$_seed . '_' . $this->fbUserId . '_' . Lib_MCrypt::$_seed);
		$this->view->contest = $contest;
		$this->view->contestants = $contestants;
	}
	
	public function voteAction() {
		if (!$this->_helper->acl->isAllowed('contest', 'actions')) {
			throw new Exception_NotSubscribed();
		}
		if (!$contestId = intval($this->_getParam('contestId'))) {
			throw new Exception_PageNotFound();
		}
		if (!$userId = intval($this->_getParam('userId'))) {
			throw new Exception_PageNotFound();
		}
		$participeTable = new Model_DbTable_Participe();
		$participeTable->voteFor($contestId, $userId);
		$this->_redirect('contest/show/id/'.$contestId);
	}
	
}