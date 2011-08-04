<?php

class Exception_PageNotFound extends Exception {
	public function Exception_PageNotFound($message = null, $code = 404) {
		if ($message)
			$this->message = null;
		else
			$this->message = "Page not found";
		if ($code != 404)
			$this->code = $code;
		else
			$code = 404;
	}
}