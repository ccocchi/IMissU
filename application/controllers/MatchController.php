<?php

require_once 'FacebookController.php';

class MatchController extends FacebookController {
	protected $_model;
	protected $_self;
	
	public function init() {
		parent::init();
		$this->requireLogin();
		$this->_model = new Model_DbTable_User();
		$this->_self = $this->_model->enableCache($this->fbUserId)->findByFbId($this->fbUserId);
	}
	
	public function indexAction() {
		try {
		$adapter = new Zend_Paginator_Adapter_DbTableSelect($this->_model->select()->order('RANDOM()'));
		} catch (Exception $e) {
			echo $e->getMessage();
		}
		$p = new Zend_Paginator($adapter);
		$p->setItemCountPerPage(1);
		$cache = Zend_Registry::get('cache');
		$p->setCache($cache);
		$p->setCacheEnabled(true);
		$this->view->paginator = $p;
		
	}
	
	
}