<?php

class Helper_Dedicaces extends Zend_Controller_Action_Helper_Abstract {
	protected $_model;
	
	public function __construct() {
		$this->_model = new Model_DbTable_Dedicace();
	}
		
	public function preDispatch() {
		// TODO: choisir le temps de cache
		$dedi = $this->_model->enableCache()->selectGetCurDedicace();
		$res = array();
		$cnt = $dedi->count() - 1;
		if ($cnt >= 0) {
			for ($i = 0; $i < 25; ++$i)
				$res[] = $dedi[rand(0, $cnt)];	
		}		

		$view = $this->getActionController()->view;
		$view->dedicaces = $res;
	}
}