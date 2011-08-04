<?php
class Form_Dedicace extends Zend_Form {
	public function init() {
		$this->addElement('textarea', 'contenu', array(
			'label' => 'Ta dédicace',
			'required' => true
		));
		$this->addElement('submit', 'submit', array(
			'label' => 'Envoyer'
		));
		
	}
}