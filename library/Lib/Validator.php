<?php
class Lib_Validator extends Zend_Validate_Abstract{
    const ALREADY_TAKEN    = 'alreadyTaken';
	
    protected $_messageTemplates = array(
        self::ALREADY_TAKEN    => "'%value%' est déjà pris par un autre utilisateur",
    );
    
  	public function isValid($value)
    {
		$model = new Model_DbTable_User();
	    $this->_setValue($value);
		
		$user = $model->findByName ($value);
		if (count ($user) != 0) {
		    $this->_error(self::ALREADY_TAKEN);
		    return false;
		}
		return true;
    }
}