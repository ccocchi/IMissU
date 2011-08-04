<?php

class Form_Comment extends Zend_Form {
	public function init() {
		$this->addElement('textarea', 'message', array(
			'required' => true
		));

		$this->addElement('submit', 'submit', array (
			'label' => 'Ajouter votre commentaire'
		));
		
		$this->clearDecorators();
		$this->addDecorator('FormElements')
			->addDecorator('HtmlTag', array(
				'tag' => '<ul>',
				'class' => 'form'
			))
			->addDecorator('Form');
			
	}
}