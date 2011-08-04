<?php

require_once 'FacebookController.php';

class MessageController extends FacebookController {
	protected $_model;
	protected $_self;
	
	public function init() {
		parent::init();
		$this->requireLogin();
		$this->_model = new Model_DbTable_Thread();
		$userTable = new Model_DbTable_User();
		$this->_self = $userTable->enableCache($this->fbUserId)->findByFbId($this->fbUserId);
		$this->view->whoami = "messagerie";
	}
	
	public function preDispatch() {
		if (!$this->_helper->acl->isAllowed('mail')) {
			throw new Exception_NotSubscribed();
		}
	}
	
	public function inboxAction() {
		$threads = $this->_model->enableCache('thread_' . $this->fbUserId)
						->findThreadsForAndByUser($this->_self->user_id)->toArray();
		
		$real = array();
		foreach($threads as $thread) {
			$tmp = $thread;
			$a = explode('.', $thread['last_message']);
			$datetime = date_create($a[0]);
			
			$date = date_format($datetime, 'Y-m-d');
			$time = date_format($datetime, 'H:i');
			
			if ($date == date('Y-m-d'))
				$tmp['date'] = $time;
			else
				$tmp['date'] = date_format($datetime, 'd M.');
			
			$tmp['contact'] = ($thread['nickname'] == $this->_self->nickname ? $thread['to'] : $thread['nickname']);
			$tmp['hash'] = Lib_MCrypt::encrypt(Lib_MCrypt::$_seed . $thread['thread_id'] . Lib_MCrypt::$_seed);
			
			$real[] = $tmp;
		}				

		$this->view->key = Lib_MCrypt::encrypt(Lib_MCrypt::$_seed . '_' . $this->fbUserId . '_' . Lib_MCrypt::$_seed);
		$this->view->threads = $real;
	}
	
	public function newAction() {	
		$form = new Form_Message();
		$this->view->form = $form;
		$to = $this->_getParam('to');
		if ($to)
			$form->setDefault('to', $to);
		
		if ($this->_request->isPost()) {
			$formData = $this->_request->getPost();
			if ($form->isValid($formData)) {
				// Vérification du destinataire
				$userTable = new Model_DbTable_User();
				$to = $userTable->findByName($formData['to']);
				if (!$to) {
					$form->getElement('to')->addError('Le destinataire n\'existe pas');
					$form->populate($formData);
					return;
				}
				
				// Nouveau thread
				$result = $this->_model->execProc('create_thread', array(
					$formData['subject'], 
					$this->_self->user_id,
					$to->user_id
				));

				// Clean du cache
				//$this->_helper->cacheCleaner->clean('thread_' . $this->fbUserId);
				
				// Ajout du message dans le thread
				$this->_model->execProc('add_message', array(
					nl2br($formData['content']),
					$result['create_thread'],
					$this->_self->user_id,
					$to->user_id
				));
				
				$this->_redirect('message/inbox');
			} else {
				$form->populate($formData);
			}			
		}
	}
	
	public function showAction() {
		$form = new Form_Answer();
		$id = $this->_getParam('id');
		
		// Thread courant
		$thread = $this->_model->enableCache()->find($id)->current();
		
		if (!$thread) {
			throw new Exception_PageNotFound();
		}
		
		// Destinataire du message
		$toId = ($this->_self->user_id == $thread->user_id ? $thread->use_user_id : $thread->user_id);
		
		if ($this->_request->isPost()) {
			$formData = $this->_request->getPost();
			$id = $formData['thread'];
			if ($form->isValid($formData)) {
				
				// Ajout du message
				$this->_model->execProc('add_message', array(
					$formData['content'],
					$formData['thread'],
					$this->_self->user_id,
					$toId
				));
				
				//$this->_helper->cacheCleaner->clean('thread_' . $this->fbUserId);
				$form->getElement('content')->setValue("");		
			} else {
				$form->populate($formData);
			}
		} else {
			$this->_model->setRead($thread->thread_id, $this->_self->user_id);
		}

		$messageTable = new Model_DbTable_Message();
		$messages = $messageTable->findMessagesByThread($id);
		
		$form->getElement('thread')->setValue($thread->thread_id);
			
		$this->view->thread = $thread;
		$this->view->messages = $messages;

		$this->view->form = $form;
		$this->view->me = $this->_self->user_id;
		$this->view->fbid = $this->_self->fbid;
		$this->view->photo = $this->_self->miniature_id;
	}
	
	public function deleteAction(){
			$id = $this->_getParam('id');
			$id = $this->_request->getParam('id');
			$threadTable = new Model_DbTable_Thread();
			$threadTable->setDeleted($id, $this->_self->user_id);
			$this->_redirect('message/inbox');
	}
}