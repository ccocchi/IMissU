<?php

class Helper_CacheCleaner extends Zend_Controller_Action_Helper_Abstract {
	protected $_cache;
	protected $_cleanIds;
	
	public function __construct() {
		$this->_cache = Zend_Registry::get('cache');
		$this->_cleanIds = array();
	}
	
	public function clean($id) {
		if (!is_array($id)) {
			$id = array($id);
		}
		$this->_cleanIds = array_merge($this->_cleanIds, $id);
	}
	
	public function postDispatch() {
		foreach ($this->_cleanIds as $id) {
			$this->_cache->remove($id);
		}
		$this->_cleanIds = array();
	}
}