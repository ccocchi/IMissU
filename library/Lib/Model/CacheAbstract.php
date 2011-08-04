<?php

class Lib_Model_CacheAbstract extends Zend_Db_Table_Abstract {
	protected $_cacheOptions = false;	

	public function enableCache($id = null, $tags = array(), $specificLifetime = false) {
		$this->_cacheOptions = array(
			'id' => $id,
			'tags' => $tags,
			'specificLifetime' => $specificLifetime);
		$this->noCache();
		return $this;
	}

	public function noCache() {
		$this->_cacheOptions = false;
		return $this;
	}

	public function execProc($name, $params = array(), $multi = false) {
		$count = count($params);
		$sql = "SELECT $name (";
		for ($i = 0; $i < $count; $i++) {
			$sql .= ':var' . $i;
			if ($i != $count - 1)
				$sql .= ', ';
		}
		$sql .= ');';
		
		try {
			$statement = $this->getAdapter()->prepare($sql);
			for ($i = 0; $i < $count; $i++) {
				$statement->bindParam(':var' . $i, $params[$i], PDO::PARAM_STR);
			}
		
			$statement->execute();
		} catch (Exception $e) {
			echo $e->getMessage();
		}
		
		if ($multi)
			return $statement->fetchAll();
		else
			return $statement->fetch();
	}


	protected function _fetch(Zend_Db_Table_Select $select) {
		if (!$this->_cacheOptions) {
			$logger = Zend_Registry::get('logger');
			$logger->log('Cache not active : ' . $select, 7);
			try {
			$result = parent::_fetch($select);
			} catch (Exception $e) {
				echo $e->getMessage();
			}
		}
		else {
			try {
				$cache = Zend_Registry::get('cache');
			} catch (Zend_Exception $e) {
				throw $e;
			}
			$defaultId = md5($select->__toString());
			$id = ($this->_cacheOptions['id'] != null ?
			$this->_cacheOptions['id'] : $defaultId);
			if (!($result = $cache->load($id))) {
				try {
				$result = parent::_fetch($select);
				$logger = Zend_Registry::get('logger');
				$logger->log('Cache SQL done : ' . $select, 7);
				$cache->save(
					$result,
					$id,
					$this->_cacheOptions['tags'],
					$this->_cacheOptions['specificLifetime']
				);
				} catch (Exception $e) {
					return;
				}
			} else {
				$logger = Zend_Registry::get('logger');
				$logger->log('Fetching from cache : ' . $select, 7);
			}
		}
		return $result;
	}
}