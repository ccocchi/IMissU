<?php

class Model_DbTable_SuperMessage extends Lib_Model_CacheAbstract {
	protected $_name = 'super_messages';
	protected $_primary = 'super_id';	
	protected $_dependentTables = array(
		'Model_DbTable_Receive'
	); 
}
