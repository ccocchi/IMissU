<?php

class Form_Message extends Zend_Form {
	public function init() {
		$this->addElement('text', 'to', array(
			'label' => 'Destinataire',
			'required' => true
		));
		$this->addElement('text', 'subject', array(
			'label' => 'Sujet',
			'required' => true
		));
		$this->addElement('textarea', 'content', array(
			'id' => 'message-content',
			'label' => 'Message',
			'required' => true,
			'rows' => 5
		));
		$this->addElement('submit', 'submit', array(
			'label' => 'Envoyer le message'
		));
		
	}
}