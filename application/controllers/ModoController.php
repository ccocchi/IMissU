<?php

require_once 'FacebookController.php';

class ModoController extends FacebookController {
	protected $_model;
	protected $_self;
	
	public function init() {
		parent::init();
		$this->requireLogin();
		$this->_model = new Model_DbTable_User();
		$this->_self = $this->_model->enableCache($this->fbUserId)->findByFbId($this->fbUserId);
	}
		
	public function modophotoAction() {
		$idPhoto = $this->_getParam('photo_id');
		$photoTable = new Model_DbTable_Photo();
		$photoTable->moderate ($idPhoto);
		
		$mail = new Zend_Mail();
		$mail->setBodyText('Une modération est demandé par un utilisateur sur la photo '.$idPhoto.'.');
		$mail->setFrom('imissu@imissu.com', 'ImissuModerateur');
		$mail->addTo('anne.lacan@gmail.com', 'un destinataire');
		$mail->setSubject('Sujet de test');
		$mail->send();
		
		$flashMessenger = $this->_helper->getHelper('FlashMessenger');
		$flashMessenger->addMessage('Votre demande a été prise en compte. Merci de votre participation.', 'error');
		$this->_redirect('');
	}
	
	public function validphotoAction() {
		$idPhoto = $this->_getParam('photo_id');
		$photoTable = new Model_DbTable_Photo();
		$photoTable->validate ($idPhoto);
	}
	
	public function modocommentAction() {
		$idComment = $this->_getParam('comment_id');
		$commentTable = new Model_DbTable_Comment();
		$commentTable->moderate ($idComment);

		$mail = new Zend_Mail();
		$mail->setBodyText('Une modération est demandé par un utilisateur sur le commentaire '.$idComment.'.');
		$mail->setFrom('imissu@imissu.com', 'ImissuModerateur');
		$mail->addTo('anne.lacan@gmail.com', 'un destinataire');
		$mail->setSubject('Sujet de test');
		$mail->send();
		
		$flashMessenger = $this->_helper->getHelper('FlashMessenger');
		$flashMessenger->addMessage('Votre demande a été prise en compte. Merci de votre participation.', 'error');
		$this->_redirect('');
	}
	
	public function validcommentAction() {
		$idComment = $this->_getParam('comment_id');
		$commentTable = new Model_DbTable_Comment();
		$commentTable->validate ($idComment);
	}
	
	public function indexAction() {
	}
}