<?php

class Helper_Acl extends Zend_Controller_Action_Helper_Abstract {
	protected $_configAcl;
	protected $_userRole;
	
	public function __construct() {
		$this->_configAcl = new Zend_Acl();
		
		$path = APPLICATION_PATH . '/configs/acl.ini';
		$ini = new Zend_Config_Ini($path);
		
		$roles = explode(',', $ini->roles);
		foreach($roles as $role) {
			$this->_configAcl->addRole(new Zend_Acl_Role($role));
		}
		
		$resources = explode(',', $ini->resources);
		foreach($resources as $resource) {
			$this->_configAcl->addResource(new Zend_Acl_Resource($resource));
		}
		
		foreach($roles as $role) {
			$current = $ini->$role;
			foreach ($current as $action => $resources) {
				if (!$resources) {
					continue;
				}
				foreach($resources as $resource => $privs) {
					$array_privs = explode(',', $privs);
					foreach ($array_privs as $priv) {
						$this->_configAcl->{$action}($role, $resource, $priv);
					}
					
				}
			}
		}
	}
		
	public function preDispatch() {
		$fbUserId = $this->getActionController()->fbUserId;
		$model = new Model_DbTable_User();
		$u = $model->enableCache()->findByFbId($fbUserId);
		
		// Vérifie si la connexion est la première de la journée
		if ($u && (!$u->last_login || $u->last_login < date('Y-m-d'))) {
			$where = $model->getAdapter()->quoteInto('fbid = ?', $fbUserId);
			$model->update(array(
				'points' => $u->points + GAIN_DAILY,
				'last_login' => 'now()'), $where);
		}

		// Vérifie le role de l'utilisateur
		if (!$u) {
			$this->_userRole = 'guest';
		}
		else {
			if ($u->is_admin)
				$this->_userRole = 'admin';
			elseif ($u->is_moderator) 
				$this->_userRole = 'modo';
			elseif ($u->is_vip)
				$this->_userRole = 'vip';
			else
				$this->_userRole = 'user';
		}
		
		//$this->_userRole = 'guest';
		$view = $this->getActionController()->view;
		if ($u)
			$view->money = $u->points;
	}
	
	public function isAllowed($resource, $privileges = '', $assert = array()) {
		return $this->_configAcl->isAllowed($this->_userRole, $resource, $privileges, $assert);
	}
	
	public function getRole() {
		return $this->_userRole;
	}
}