<?php

class Model_DbTable_Favorite extends Lib_Model_CacheAbstract {
	protected $_name = "favorite";
	protected $_primary = array('user_id', 'use_user_id');
	protected $_referenceMap = array(
		'owner' => array (
			'columns'		=> 'user_id',
			'refTableClass'	=> 'Model_DbTable_User',
			'refColumns'	=> 'user_id'
		),
		'favorite' => array (
			'columns'		=> 'use_user_id',
			'refTableClass'	=> 'Model_DbTable_User',
			'refColumns'	=> 'user_id'
		)
	);
}