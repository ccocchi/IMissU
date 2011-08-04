<?php

class Form_Contest extends Zend_Form {
	public function init() {
		$this->addElement('text', 'title', array(
			'label' => 'Titre',
			'required' => true
		));
		$this->addElement('textarea', 'description', array(
			'label' => 'Description',
			'rows' => 5
		));
		$this->addElement('text', 'date_begin', array(
			'label' => 'date début',
			'required' => true
		));
		$this->addElement('text', 'date_end', array(
			'label' => 'date fin',
			'required' => true
		));
		$this->addElement('submit', 'submit', array(
			'label' => 'Valider'
		));
		
		
		$this->getElement('date_begin')->addValidator(new Zend_Validate_Date());
		$this->getElement('date_begin')->setErrorMessages (array ("La date fournie n'est pas valide, elle doit respectée le format : YYYY-MM-DD"));
		$this->getElement('date_end')->addValidator(new Zend_Validate_Date());
		$this->getElement('date_end')->setErrorMessages (array ("La date fournie n'est pas valide, elle doit respectée le format : YYYY-MM-DD"));
		
	}
}