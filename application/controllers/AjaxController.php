<?php

require_once 'FacebookController.php';

class AjaxController extends FacebookController {
	public function init() {
		parent::init();
		$userTable = new Model_DbTable_User();
		$hash = $this->getRequest()->getParam('key');
		if (!$hash) {
			exit('No key');
		}
		
		$decode = Lib_MCrypt::decrypt($hash);
		$array = explode('_', $decode);
		
		if ($array[0] != Lib_MCrypt::$_seed) {
			exit('Hack');
		}
		
		$this->fbUserId = $array[1];
		$this->_self = $userTable->enableCache($this->fbUserId)->findByFbId($this->fbUserId);
		$this->_model = new Model_DbTable_Vote();

		// Désactive le rendu de la vue
		$this->getHelper('viewRenderer')->setNoRender(true);
		$this->getHelper('layout')->disableLayout();
	}
	
	public function voteAction() {
		$hash = $this->_getParam('hash');
		$value = intval($this->_getParam('value'));
		
		$decode = Lib_MCrypt::decrypt($hash);
		$array = explode('_', $decode);
		
		if ($array[1] != Lib_MCrypt::$_seed) {
			return false;
		}
		if ($value <= 0 || $value > 3) {
			return false;
		}
		
		$target = intval($array[0]);
		$date = $array[2];
		
		$this->_model->execProc('add_vote', array(
			$this->_self->user_id,
			$target,
			$value
		));
		
		return true;
	}
	
	public function voteprofileAction() {
		$hash = $this->_getParam('hash');
		
		$decode = Lib_MCrypt::decrypt($hash);
		$array = explode('_', $decode);
		
		if ($array[1] != Lib_MCrypt::$_seed) {
			echo 'false';
			return;
		}
		
		$userTable = new Model_DbTable_User();
		if (!$userTable->hasEnoughPoints($this->_self->user_id, COST_VOTE)) {
			echo json_encode(array('message' => "Desole, tu n'as pas suffisament de points.&nbsp;",
				'res' => false));
			return;
		}
		
		$target = intval($array[0]);
		$date = $array[2];
		
		$this->_model->execProc('add_vote', array(
			$this->_self->user_id,
			$target,
			VALUE_VOTE_PROFIL
		));
		
		echo json_encode(array('message' => "Ton vote a bien ete pris en compte.",
			'res' => true));
	}
	
	
	public function nextAction() {
		$data = $this->_getParam('data');
		$userTable = new Model_DbTable_User();
		$select = $userTable->select()
				->where("miniature_id IS NOT NULL");
		/* TODO: USER RIGHTS */
		if ($data['sexe'] != '')
			$select->where("? = '' OR sex = ?", $data['sexe']);
		if ($data['age'] != '')
			$select->where("? = '' OR EXTRACT(YEAR FROM AGE(birthday)) > ?", $data['age']);
		$users = $userTable->enableCache()->fetchAll($select);
		$offset = rand(0, $users->count() - 1);
		if ($users->count() == 0)
		{
			//TODO: handle error
			echo json_encode(array(
					'path' => "http://www.google.fr/intl/en_com/images/logo_plain2.png",
					'hash' => ""
			));			
			return;
		}
		$current = $users->getRow($offset);
		$pictureName = Lib_Namer::pictureName($current->fbid, $current->miniature_id);
		$dir = USER_IMAGE_DIR_PATH;
		$path = $this->view->baseUrl($dir . $pictureName);
		$hash = Lib_MCrypt::encrypt($current->user_id . '_' . Lib_MCrypt::$_seed . '_' . date('Y-m-d'));
		
		echo json_encode(array(
				'path' => $path,
				'hash' => $hash,
				'username' => $current->nickname
		));
	}
	
	public function flashAction() {
		$hash = $this->_getParam('hash');
		
		$decode = Lib_MCrypt::decrypt($hash);
		$array = explode('_', $decode);
		
		if ($array[1] != Lib_MCrypt::$_seed) {
			return false;
		}
		
		$userTable = new Model_DbTable_User();
		if (!$userTable->hasEnoughPoints($this->_self->user_id, COST_FLASH)) {
			//echo "Vous n'avez plus assez de point pour effectuer cette action";
			//echo '<div class="in-fancybox">Vous n\'avez plus assez de point pour effectuer cette action</div>';
			return false;
		}
		
		$target = intval($array[0]);
		$date = $array[2];
		
		$subject = $this->_self->nickname . ' a flashé sur vous !';
		$content = $this->_self->nickname . ' a flashé sur vous. N\'hésitez pas à lui répondre ou à aller sur son profil';
		
		$this->_model->execProc('add_flash', array(
			$this->_self->user_id,
			$target,
			$subject,
			$content,
			10
		));
		
		//echo 'Vous venez de flashez.';
		//echo '<div class="in-fancybox">Votre flash a été pris en compte.</div>';
		return true;
	}
	
	public function flashprofileAction() {
		$hash = $this->_getParam('hash');
		
		$decode = Lib_MCrypt::decrypt($hash);
		$array = explode('_', $decode);
		
		if ($array[1] != Lib_MCrypt::$_seed) {
			return false;
		}
		
		$userTable = new Model_DbTable_User();
		if (!$userTable->hasEnoughPoints($this->_self->user_id, COST_FLASH)) {
			echo json_encode(array('message' => "Desole, tu n'as pas suffisament de points.&nbsp;",
				'res' => false));
			return;
		}
		
		$target = intval($array[0]);
		$date = $array[2];
		
		$subject = $this->_self->nickname . ' a flashé sur vous !';
		$content = $this->_self->nickname . ' a flashé sur vous. N\'hésitez pas à lui répondre ou à aller sur son profil';
		
		$this->_model->execProc('add_flash', array(
			$this->_self->user_id,
			$target,
			$subject,
			$content,
			10
		));
		
		
		echo json_encode(array('message' => "Ton flash a bien ete pris en compte. &nbsp;",
			'res' => true));
	}
	
	
	public function votecontestAction() {
		$contest_id = intval($this->_getParam('contest_id'));
		$nickname = $this->_getParam('nickname');
		
		$userTable = new Model_DbTable_User();
		$participeTable = new Model_DbTable_Participe();
		if (!$userTable->hasEnoughPoints($this->_self->user_id, COST_VOTE)) {
			echo json_encode(array('message' => "Desole, Tu n'as pas suffisament de points.&nbsp;",
				'res' => false));
			return;
		}
		
		$user = $userTable->findByName($nickname);
		$participeTable->voteFor($contest_id, $user->user_id);
		echo json_encode(array('message' => "Ton vote a bien ete pris en compte.",
				'res' => true));
	}
	
	
	public function votegeneralAction() {
		$nickname = $this->_getParam('nickname');
		$userTable = new Model_DbTable_User();
		if (!$userTable->hasEnoughPoints($this->_self->user_id, COST_VOTE)) {
			echo json_encode(array('message' => "Desole, Tu n'as pas suffisament de points.&nbsp;",
				'res' => false));
			return;
		}
		
		$target = $userTable->findByName($nickname);
		
		$this->_model->execProc('add_vote', array(
			$this->_self->user_id,
			$target->user_id,
			VALUE_VOTE_PROFIL
		));
		
		echo json_encode(array('message' => "Ton vote a bien ete pris en compte.",
				'res' => true));
	}
	
	public function favoriteAction() {
		$hash = $this->_getParam('hash');
		
		$decode = Lib_MCrypt::decrypt($hash);
		$array = explode('_', $decode);
		
		if ($array[1] != Lib_MCrypt::$_seed) {
			echo '<div id="flash-error">Utilisateur non reconnu</div>';
			return;
		}
		
		$target = $array[0];
		
		$favoriteTable = new Model_DbTable_Favorite();
		$return = $favoriteTable->execProc('add_favorite', array(
			$this->_self->user_id, $target
		));
		
		if (!$return['add_favorite']) {
			echo '<div class="in-fancybox">Ce contact est déja présent dans votre liste de favoris.</div>';
			return;
		}
		
		echo '<div id="flash-notice">Le contact a été ajouté à votre liste de favoris.</div>';
	}
	
	public function deleteAction() {
		if ($this->_request->isPost()) {
			$id = $this->_request->getParam('id');
			$threadTable = new Model_DbTable_Thread();
			$threadTable->setDeleted($id, $this->_self->user_id);
		}
	}
	
	public function usersAction() {
		$type = $this->_getParam('type');
		$page = $this->_getParam('page');
		
		$userTable = new Model_DbTable_User();
		
		if ($type == "male") {
			$select = $userTable->selectFindAllMale();	
		} else if ($type == "female") {
			$select = $userTable->selectFindAllFemale();
		}
		
		$adapter = new Lib_Paginator_Adapter_CacheDbTableSelect($select);
		$p = new Zend_Paginator($adapter);
		$p->setItemCountPerPage(10);
		$p->setCurrentPageNumber($page);
			
		$res = array();
		foreach ($p as $user) {
			$data = array();
			$login = $user->nickname;
			$pictureName = Lib_Namer::pictureName($user->fbid, $user->miniature_id, 'jpg', 'small');
			$dir = USER_THUMB_DIR_PATH;
			$path = $this->view->baseUrl($dir . $pictureName);
			$data['path'] = $path;
			$data['votes'] = $user->vote;
			$res[$login] = $data;
		}
		
		echo json_encode($res);
	}
	
	public function commentsAction() {
		$page = intval($this->_getParam('page'));
		$id = intval($this->_getParam('user'));
		
		$commentTable = new Model_DbTable_Comment();
		$select = $commentTable->selectFindCommentsFor($id);
		
		$adapter = new Zend_Paginator_Adapter_DbTableSelect($select);
		$p = new Zend_Paginator($adapter);
		$p->setItemCountPerPage(10);
		$p->setCurrentPageNumber($page);
		
		$res = array();
		foreach ($p as $message) {
			$data = array();
			//$pictureName = Lib_Namer::pictureName($user->fbid, $user->miniature_id, 'jpg', 'small');
			//$dir = USER_THUMB_DIR_PATH;
			//$path = $this->view->baseUrl($dir . $pictureName);
			//$data['path'] = $path;
			$dir = USER_THUMB_DIR_PATH;
			$name = Lib_Namer::pictureName($user->fbid,  $user->miniature_id, 'jpg', 'small');		
			$data['thumb'] = $this->view->baseUrl($dir . $name);
			$data['name'] = $message->nickname;
			$data['date'] = Lib_DateTool::formatSqlDate($message->date);
			$data['comment'] = nl2br(stripslashes($message->message));
			$res[] = $data;
		}
		
		echo json_encode($res);
	}
	
	public function setprofileAction() {
		//if (!$this->_helper->acl->isAllowed('otheruser', 'otheruser')) {
		//	throw new Exception_NotSubscribed();
		//}
		
		$id = intval($this->_getParam('pid'));
		
		// TODO : vérifiaction du propiétaire de la photo
		$userTable = new Model_DbTable_User();
		$where = $userTable->getAdapter()->quoteInto('fbid = ?', $this->fbUserId);
		$userTable->update(array(
			'miniature_id' => $id
		), $where);
		
		echo '<div class="in-fancybox">Photo de profil changée avec succès</div>';
	}
	
		
	public function usephotoAction(){
			$pic_id = $this->_getParam('id');
			$userTable = new Model_DbTable_User();
			$userTable->changeProfilePhoto($this->_self->user_id, $pic_id);
	}
	
		
	public function delcommentAction(){
			$id = $this->_getParam('id');
			$comTable = new Model_DbTable_Comment();
			$comTable->deleteComment($id);
	}
	
	
}