<?php

require_once 'FacebookController.php';

class UserController extends FacebookController {
	protected $_model;
	protected $_self;
	
	public function init() {
		parent::init();
		$this->requireLogin();
		$this->_model = new Model_DbTable_User();
		$this->_self = $this->_model->enableCache($this->fbUserId)->findByFbId($this->fbUserId);
		$this->view->whoami = "profile";
	}
	
	public function preDispatch() {
		$actionName = $this->_request->getActionName();
				
		if ($actionName == "subscribe" && !$this->_helper->acl->isAllowed('user', 'subscribe')) {
           throw new Exception_NoPrivileges();
        }

        if (($actionName == "profile" && !$this->_helper->acl->isAllowed('user', 'profile')
        && !$this->_helper->acl->isAllowed('user')) || ($actionName != "profile" && $actionName != "subscribe" && !$this->_helper->acl->isAllowed('user'))) {
        	throw new Exception_NotSubscribed();
       	}
	}
		
	public function editAction() {
		$form = new Form_User();
		$data = $this->_self->toArray();
		$data = array_map(stripslashes, $data);
		$form->populate($data);
		$this->view->form = $form;
		if ($this->_request->isPost()) {
			$formData = $this->_request->getPost();
			if ($form->isValid($formData)) {
				unset($formData['submit']);
				$where = $this->_model->getAdapter()->quoteInto('fbid = ?', $this->fbUserId);
				$this->_model->update($formData, $where);
				$cache = Zend_Registry::get('cache');
				$cache->remove($this->fbUserId);
				$this->_redirect('user/profile');
			} else {
				$form->populate($formData);
			}
		}
	}
	
	public function photosAction() {
		if (!$username = $this->_getParam('username')) {
			$user = $this->_self;
		} else {
			$user = $this->_model->findByName($username);
		}
		
		if (!$user) {
			throw new Exception_PageNotFound();
		}
		
		$photoTable = new Model_DbTable_Photo();
		$pictures = $photoTable->findPhotosForUser($user->user_id);
		
		$this->view->isSelf = $user->user_id == $this->_self->user_id;
		$this->view->user = $user;
		$this->view->pictures = $pictures;
		$this->view->key = Lib_MCrypt::encrypt(Lib_MCrypt::$_seed . '_' . $this->fbUserId . '_' . Lib_MCrypt::$_seed);
		
	}

	
	public function photoAction() {
		$username = $this->_getParam('username');
		$id = intval($this->_getParam('id'));
		
		$user = $this->_model->findByName($username);
		
		if (!$user) {
			throw new Exception_PageNotFound();
		}
		
		$photoTable = new Model_DbTable_Photo();
		$picture = $photoTable->findPhotoByIdAndUser($id, $user->user_id);
		
		if (!$picture) {
			throw new Exception_PageNotFound();
		}
		
		$this->view->picture = $picture;
		$this->view->user = $user;
		$this->view->isSelf = $user->user_id == $this->_self->user_id;
		$this->view->key = Lib_MCrypt::encrypt(Lib_MCrypt::$_seed . '_' . $this->fbUserId . '_' . Lib_MCrypt::$_seed);
	}
	
	public function pictureAction() {
		$form = new Form_Picture();
		if ($this->_request->isPost()) {
			$formData = $this->_request->getPost();
			if ($form->isValid($formData)) {
				
				// Ajout de la photo dans la BDD
				
				$id_photo = $this->_model->execProc('add_photo', array(
						$this->_self->user_id,
						10
					));
					$id = $id_photo['add_photo'];
					$this->view->money = $this->_self->points + 10;
					$imageTypes = array(
					'image/png' => '.png',
					'image/jpeg' => '.jpg',
					'image/gif' => '.gif'
					);
				
				// Vérification de l'upload
				$upload = new Zend_File_Transfer_Adapter_Http();
				//$upload->addValidator('MimeType', false, array('image/jpeg', 'image/gif', 'image/png'));
				//$upload->addValidator('FilesSize', false, '500kB');
				
				if ($upload->isValid()) {
					$info = $upload->getFileInfo('file');
					$tmp_name = $info['file']['tmp_name'];
					
					// Original
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
				 	$name = Lib_Namer::pictureName($this->fbUserId, $id, 'jpg', 'small');
					$thumb->save(USER_THUMB_DIR_PATH . $name, 'jpg');

				} else {
					$form->populate($formData);
				}
			}
		}
		$this->view->form = $form;
	}
	
	public function profileAction() {
		$this->view->canCom = $this->_helper->acl->isAllowed('user', 'comment');

		$form = new Form_Comment();
		$commentModel = new Model_DbTable_Comment();
		
		// Recherche de l'utilisateur
		if (!($username = $this->_getParam('username'))) {
			//if (!$id = $this->_getParam('id')) {
			$user = $this->_self;
			//} else {
			//	$user = $this->_model->noCache()->findById($id);
			//}
		}
		else
		$user = $this->_model->noCache()->findByName($username);

		// User not found
		if (!$user) {
			throw new Exception_PageNotFound();
		}

		if ($this->_request->isPost()) {
			if (!$this->_helper->acl->isAllowed('cancomment')) {
				throw new Exception_NotSubscribed();
			}
			
			// Not enough point for action
			if (!$this->_model->hasEnoughPoints($this->_self->user_id, COST_COMMENT)) {
				throw new Exception_NotEnoughPoints();
			}
			
			$this->_self->points -= COST_COMMENT;
			
			$formData = $this->_request->getPost();
			if ($form->isValid($formData)) {
				$commentModel->insert(array(
					'user_id' => $this->_self->user_id,
					'use_user_id' => $user->user_id,
					'message' => $formData['message'],
					'date' => 'now()'
					));

					$form->getElement('message')->setValue('');
			} else {
				$form->populate($formData);
			}
		}
			
		$like = explode(';', $user->LIKE);
		$dislike = explode(';', $user->dislike);

		$page = $this->_getParam('page', 1);
		$select = $commentModel->selectFindCommentsFor($user->user_id);
		
		$adapter = new Zend_Paginator_Adapter_DbTableSelect($select);
		$p = new Zend_Paginator($adapter);
		$p->setItemCountPerPage(10);
		$p->setCurrentPageNumber($page);
		
		$this->view->isSelf = $user == $this->_self;
		$this->view->user = $user;
		//$this->view->like = $like;
		//$this->view->dislike = $dislike;
		$this->view->comments = $p;
		$this->view->favorites = $this->_model->findFavoritesForUser($user->user_id);
		
		$this->view->form = $form;
		$this->view->key = Lib_MCrypt::encrypt(Lib_MCrypt::$_seed . '_' . $this->fbUserId . '_' . Lib_MCrypt::$_seed);
		$this->view->hash = Lib_MCrypt::encrypt($user->user_id . '_' . Lib_MCrypt::$_seed . '_' . date('Y-m-d'));
	}
	
	public function setprofileAction() {
		$id = $this->_getParam('id');
		$where = $this->_model->getAdapter()->quoteInto('fbid = ?', $this->fbUserId);
		$this->_model->update(array(
			'miniature_id' => $id
		), $where);
		$this->_redirect('user/photo/id/' . $id);
	}
	
	public function favoritesAction() {
	}
	
	public function subscribeAction() {
//		if (!$this->_self) {
//			$this->_redirect('user/profile');
//			return;
//		}
		$form = new Form_Subscribe();
		if ($this->_request->isPost()) {

			$form->getElement('nickname')->addValidator(new Lib_Validator());
		
			$formData = $this->_request->getPost();
			if ($form->isValid($formData)) {
				unset($formData['submit']);
				$formData["fbid"] = $this->fbUserId;
				$this->_model->insert($formData);			
				$cache = Zend_Registry::get('cache');
				$cache->remove($this->fbUserId);
				$this->_redirect('user/profile');
			} else {
				$form->populate($formData);
			}
		}
		$this->view->form = $form;
	}
	
	public function fansAction(){
		$id = $this->_self->user_id;
		$userTable = new Model_DbTable_User ();
		$users = $userTable->findVotersForUser($id);

		$this->view->users = $users;
	}
	
	public function changephotoAction(){
		$photoTable = new Model_DbTable_Photo();
		$pictures = $photoTable->enableCache()->fetchAll($photoTable->select()->where('user_id = ?', $this->_self->user_id));
	
		$form = new Form_Picture();
		
		if ($this->_request->isPost()) {
			$formData = $this->_request->getPost();
			if ($form->isValid($formData)) {
				// Vérification de l'upload
				$upload = new Zend_File_Transfer_Adapter_Http();
				
				if ($upload->isValid()) {
					
					$id_photo = $this->_model->execProc('add_photo', array(
						$this->_self->user_id,
						10
					));
					
					$this->_self->points += 10;
					//rajouter la verification du nombre de photo.
					
					// Ajout de la photo dans la BDD
					
					$info = $upload->getFileInfo('file');
					$tmp_name = $info['file']['tmp_name'];
					
					// Original for profile
					$thumb = PhpThumb_PhpThumbFactory::create($tmp_name);
					$thumb->resize(650);
					$name = Lib_Namer::pictureName($this->fbUserId, $id_photo['add_photo'], 'jpg');
					$thumb->save(USER_IMAGE_DIR_PATH . $name, 'jpg');
					
					// Profile image
					$thumb = PhpThumb_PhpThumbFactory::create($tmp_name);
					$thumb->resize(200);
					$name = Lib_Namer::pictureName($this->fbUserId, $id_photo['add_photo'], 'jpg', 'profile');
					$thumb->save(USER_THUMB_DIR_PATH . $name, 'jpg');
					
					// Small image
					$thumb = PhpThumb_PhpThumbFactory::create($tmp_name);
					$thumb->resize(130);
					$name = $name = Lib_Namer::pictureName($this->fbUserId, $id_photo['add_photo'], 'jpg', 'small');
					$thumb->save(USER_THUMB_DIR_PATH . $name, 'jpg');
					
					
					// Classement image
					$thumb = PhpThumb_PhpThumbFactory::create($tmp_name);
					$thumb->adaptiveResize(160, 200);
					$name = Lib_Namer::pictureName($this->fbUserId, $id_photo['add_photo'], 'jpg', 'classement');
					$thumb->save(USER_THUMB_DIR_PATH . $name, 'jpg');
					
					// Mini image
					$thumb = PhpThumb_PhpThumbFactory::create($tmp_name);
					$thumb->adaptiveResize(60, 60);
					$name = Lib_Namer::pictureName($this->fbUserId, $id_photo['add_photo'], 'jpg', 'mini');
					$thumb->save(USER_THUMB_DIR_PATH . $name, 'jpg');
					
					$userTable = new Model_DbTable_User();
					$userTable->changeProfilePhoto($this->_self->user_id, $id_photo['add_photo']);
			
					$this->_redirect('user/profile');
				} else {
					$form->populate($formData);
				}
			} else {
				$form->populate($formData);
			}
		}
		
		$this->view->form = $form;
		$this->view->pictures = $pictures;
		$this->view->user = $this->_self;
		$this->view->key = Lib_MCrypt::encrypt(Lib_MCrypt::$_seed . '_' . $this->fbUserId . '_' . Lib_MCrypt::$_seed);
	}
	
		
	public function delphotoAction() {
		$id = intval($this->_getParam('id'));
		$nick = $this->_getParam('nickname');
		$photoTable = new Model_DbTable_Photo();
		if ($nick == $this->_self->nickname){
			$photoTable->del ($id);
			$name = Lib_Namer::pictureName($this->_self->fbid,$id);
			unlink(USER_IMAGE_DIR_PATH . '/' . $name);
			$name = Lib_Namer::pictureName($this->_self->fbid,$id, 'jpg', 'small');
			unlink(USER_THUMB_DIR_PATH . '/' . $name);
			$name = Lib_Namer::pictureName($this->_self->fbid,$id, 'jpg', 'profile');
			unlink(USER_THUMB_DIR_PATH . '/' . $name);
			$name = Lib_Namer::pictureName($this->_self->fbid,$id, 'jpg', 'mini');
			unlink(USER_THUMB_DIR_PATH . '/' . $name);
		}
		$this->_redirect('user/' . $nick . '/photos');
	}

}

