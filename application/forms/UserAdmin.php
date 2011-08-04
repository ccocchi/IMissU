<?php

class Form_UserAdmin extends Zend_Form {
	public function init() {
		$this->addElement('text', 'nickname', array(
			'label' => 'Le pseudo',
			'required' => true
		));
		$this->addElement('text', 'birthday', array(
			'label' => 'La date de naissance',
			'required' => true
		));
		
		$this->addElement('text', 'fbid', array(
			'label' => 'Le Facebook Id',
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
		
		
		$sex = new Zend_Form_Element_Radio('is_vip');
		$sex->setLabel('VIP ?')
			->setRequired(true)
			->addMultiOptions(array(
			'1' => 'Oui',
			'0' => 'Non'
			));
		$this->addElement($sex);
		
		$sex = new Zend_Form_Element_Radio('is_admin');
		$sex->setLabel('Admin ?')
			->setRequired(true)
			->addMultiOptions(array(
			'1' => 'Oui',
			'0' => 'Non'
			));
		$this->addElement($sex);
		
		$sex = new Zend_Form_Element_Radio('is_moderator');
		$sex->setLabel('Modérateur ?')
			->setRequired(true)
			->addMultiOptions(array(
			'1' => 'Oui',
			'0' => 'Non'
			));
		$this->addElement($sex);
		
		$this->addElement('textarea', 'LIKE', array(
			'label' => 'Il aime',
			'rows'	=> 5
		));
		$this->addElement('textarea', 'dislike', array(
			'label' => 'Il n\'aime pas',
			'rows'	=> 5
		));
		$this->addElement('textarea', 'bio', array(
			'label' => 'La biographie',
			'rows'	=> 5
		));
		$this->addElement('submit', 'submit', array(
			'label' => 'ajouter'
		));
		
		$this->getElement('birthday')->addValidator(new Zend_Validate_Date());
		$this->getElement('birthday')->setErrorMessages (array ("La date fournie n'est pas valide, elle doit respectée le format : YYYY-MM-DD"));
		$this->getElement('nickname')->addValidator('Alnum');
		$this->getElement('nickname')->setErrorMessages (array ("Cet élément ne doit contenir que des chiffres ou des caractères"));
	}
}