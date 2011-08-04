<?php

class Form_Answer extends Zend_Form {
	public function init() {
		$this->addElement('textarea', 'content', array(
			'id' => 'message-content',
			'label' => 'Réponse',
			'required' => true,
			'rows' => 5
		));
		$this->addElement('hidden', 'thread');
		$this->addElement('submit', 'submit', array(
			'label' => 'Repondre'
		));
	}
}