<?php

require_once 'FacebookController.php';

class VipController extends FacebookController {
	protected $_model;
	protected $_self;
	
	public function init() {
		parent::init();
		$this->requireLogin();
		$this->_model = new Model_DbTable_User();
		$this->_self = $this->_model->enableCache($this->fbUserId)->findByFbId($this->fbUserId);
		$this->view->whoami = "Vip";
	}
	
	
	public function checkAction()
	{
		$RECALL = urlencode($_GET["RECALL"]);
		$AUTH = urlencode("215470/851062/3973697"); 
		$ret = @file("http://www.allopass.com/check/vf.php4?CODE=".$RECALL."&AUTH=".$AUTH);
	
		$this->view->success = true;
		
		if (ereg('ERR', $ret[0]) || ereg('NOK', $ret[0])) 
			$this->view->success = false;
		else
		{
			if ($_GET["DATAS"] == "1")
			{
				if (!$this->_self->end_vip < date("Y:m:d"))
					$this->_model->createnewVip($this->fbUserId);
				else
					$this->_model->updateVip($this->fbUserId);
				$this->view->vip = false;
			}
			else
			{
				$this->_model->addPoints($this->fbUserId, 1000);
				$this->view->vip = true;
			}	
			$cache = Zend_Registry::get('cache');
			$cache->remove($this->fbUserId);
			$this->view->vip = true;
		}
	}
	
	public function indexAction() {
		$form = new Form_Dedicace();
		$dediTable = new Model_DbTable_Dedicace();
		
		$this->view->form = $form;
		$dedi = $dediTable->getDediUSer ($this->_self->user_id);
		if ($this->_self->is_vip)
			if ($dedi)
				$form->setDefault('contenu', $dedi->content);
		if ($this->_request->isPost()) {
			$formData = $this->_request->getPost();
			if ($form->isValid($formData)) {
				unset($formData['submit']);
				$formData["user_id"] = $this->_self->user_id;
				$formData["content"] = $formData["contenu"];
				unset($formData['contenu']);
				
				$formData["date_end"] = date("Y/m/d", mktime(0,0,0,date("m")+1,date("d"),date("Y")));				
				$dediTable->insert($formData);
			} else {
				$form->populate($formData);
			}
		}
		$this->view->form = $form;
		$this->view->user = $this->_self;
		$this->view->favorites = $this->_model->findFavoritesForUser($this->_self->user_id);
		$this->view->voters = $this->_model->findVotersForUser($this->_self->user_id);
	}
}