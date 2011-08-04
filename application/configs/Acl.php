/<?php

class Config_Acl extends Zend_Acl {
	public function __construct() {
		$this->_setRolesInit();
		$this->_setResourcesInit();
		
		$this->allow('admin', 'contest', '');
		$this->deny('guest', 'messagerie');
		
	}
	
	protected function _setRolesInit() {
		$this->addRole(new Zend_Acl_Role('guest'));
		$this->addRole(new Zend_Acl_Role('user'));
		$this->addRole(new Zend_Acl_Role('vip'));
		$this->addRole(new Zend_Acl_Role('modo'));
		$this->addRole(new Zend_Acl_Role('admin'));
	}
	
	protected function _setResourcesInit() {
		$this->addResource(new Zend_Acl_Resource('contest'));
		$this->addResource(new Zend_Acl_Resource('messagerie'));
	}
}