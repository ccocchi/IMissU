<?php

class Form_Picture extends Zend_Form {
	
	public function init() {
		$this->addElement('file', 'file', array(
			'label' => 'Choissisez le fichier',
			'required' => true
		));
		$this->addElement('submit', 'submit', array(
			'label' => 'Ajouter la photo'
		));
		$this->setEnctype('multipart/form-data');
	}
}