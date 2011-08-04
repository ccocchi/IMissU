<?php

class Model_DbTable_Visit extends Lib_Model_CacheAbstract {
	protected $_name = 'visite';
	protected $_primary = 'visit_id';
	protected $_referenceMap = array(
		'visitor' => array (
			'columns'		=> 'user_id',
			'refTableClass'	=> 'Model_DbTable_User',
			'refColumns'	=> 'user_id'
		),
		'target' => array (
			'columns'		=> 'use_user_id',
			'refTableClass'	=> 'Model_DbTable_User',
			'refColumns'	=> 'user_id'
		)
	);
}