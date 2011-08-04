<?php

require_once 'FacebookController.php';

class FavoriteController extends FacebookController {
	protected $_model;
	protected $_self;
	
	public function init() {
		parent::init();
		$this->requireLogin();
		$this->_model = new Model_DbTable_User();
		$this->_self = $this->_model->enableCache($this->fbUserId)->findByFbId($this->fbUserId);
		$this->view->errorMessages = $this->_helper->FlashMessenger->getMessages('error');
	}
	
	public function preDispatch() {
		if (!$this->_helper->acl->isAllowed('favorite')) {
			throw new Exception_NotSubscribed();
		}
	}
	
	public function indexAction() {
		$favorites = $this->_model->findFavoritesForUser($this->_self->user_id);
		
		$this->view->favorites = $favorites;
	}
	
	public function addAction() {
		$id = intval($this->_getParam('id'));
		$favoriteTable = new Model_DbTable_Favorite();
		
		$return = $favoriteTable->execProc('add_favorite', array(
			$this->_self->user_id, $id
		));
		
		if (!$return['add_favorite']) {
			$flashMessenger = $this->_helper->getHelper('FlashMessenger');
			$flashMessenger->addMessage('Ce contact est déjà dans vos favoris', 'error');
		}	
		
		$this->_redirect('favorite/');
	}
}