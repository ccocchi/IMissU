<?php

class Model_DbTable_Receive extends Lib_Model_CacheAbstract {
	protected $_name = 'receive';
	protected $_primary = array('super_id', 'user_id');
	protected $_referenceMap = array (
		'message' => array (
			'columns'		=> 'super_id',
			'refTableClass'	=> 'Model_DbTable_SuperMessage',
			'refColumns'	=> 'super_id'
		),
		'user' => array (
			'columns'		=> 'user_id',
			'refTableClass'	=> 'Model_DbTable_User',
			'refColumns'	=> 'user_id'	
		)
	);
}