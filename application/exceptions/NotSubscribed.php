<?php

class Exception_NotSubscribed extends Exception {
	public function Exception_NotSubscribed($message = null, $code = 200) {
		if ($message)
			$this->message = null;
		else
			$this->message = "Vous n'avez pas accès à cette partie du site. DEVIENS VIP";
		
		if ($code != 200)
			$this->code = $code;
		else
			$this->code = 200;
	}
}	