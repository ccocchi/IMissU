<?php

class Exception_NotEnoughPoints extends Exception {
	public function Exception_NotEnoughPoints($message = null, $code = 200) {
		if ($message)
			$this->message = null;
		else
			$this->message = "Vous n'avez pas assez de points pour effectuer cette action";
		if ($code != 200)
			$this->code = $code;
		else
			$code = 200;
	}
}