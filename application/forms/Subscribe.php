<?php

class Form_Subscribe extends Zend_Form {
	public function init() {
		$this->addElement('text', 'nickname', array(
			'label' => 'Ton pseudo',
			'required' => true
		));
		$this->addElement('text', 'birthday', array(
			'label' => 'Ta date de naissance',
			'required' => true
		));
		
		$sex = new Zend_Form_Element_Radio('sex');
		$sex->setLabel('Sexe')
			->setRequired(true)
			->addMultiOptions(array(
			'1' => 'Homme',
			'0' => 'Femme'
			));
		$this->addElement($sex);
		
		$this->addElement('textarea', 'LIKE', array(
			'label' => 'J\'aime',
			'rows'	=> 5
		));
		$this->addElement('textarea', 'dislike', array(
			'label' => 'Je n\'aime pas',
			'rows'	=> 5
		));
		$this->addElement('textarea', 'bio', array(
			'label' => 'Ta biographie',
			'rows'	=> 5
		));
		$this->addElement('submit', 'submit', array(
			'label' => 'S\'inscrire'
		));
		
		$this->getElement('birthday')->addValidator(new Zend_Validate_Date());
		$this->getElement('birthday')->setErrorMessages (array ("La date fournie n'est pas valide, elle doit respectée le format : YYYY-MM-DD"));
		
		$this->getElement('nickname')->addValidator('Alnum');	
		$this->getElement('nickname')->setErrorMessages (array ("Cet élément ne doit contenir que des chiffres ou des caractères"));
		
	}
}