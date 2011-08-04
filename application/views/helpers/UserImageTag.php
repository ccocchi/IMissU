<?php

class View_Helper_UserImageTag extends Zend_View_Helper_Abstract {
	protected $_allowed_options;
	
	public function __construct() {
		$this->_allowed_options = array('width', 'height', 'alt', 'class', 'id');
	}
	
	public function userImageTag($fbId, $photoId, $extension, $prefix = 'original', $options = array()) {
		$dir = ($prefix != 'original' ? USER_THUMB_DIR_PATH : USER_IMAGE_DIR_PATH);
		$name = Lib_Namer::pictureName($fbId, $photoId, $extension, $prefix);		
		
		$path = $this->view->baseUrl($dir . $name);		
		
		$opts = "";
		foreach ($this->_allowed_options as $opt) {
			if (array_key_exists($opt, $options)) {
				$opts .= $opt . '="' . $options[$opt] . '" ';
			}
		}
		
		return '<img src="' . $path . '" ' . $opts . ' />';
	}
}