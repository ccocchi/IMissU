<?php

class View_Helper_ContestImageTag extends Zend_View_Helper_Abstract {
	protected $_allowed_options;
	
	public function __construct() {
		$this->_allowed_options = array('width', 'height', 'alt');
	}
	
	public function contestImageTag($contestId, $userId, $extension = 'jpg', $prefix = 'original', $options = array()) {
		$dir = ($prefix != 'original' ? CONTEST_THUMB_DIR_PATH : CONTEST_IMAGE_DIR_PATH);
		$dir .= $contestId . '/';
		$name = Lib_Namer::contestPictureName($userId, $extension, $prefix);		
		
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