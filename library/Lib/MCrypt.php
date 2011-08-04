<?php

class Lib_MCrypt {	
	protected static $_keySize = 32;
	protected static $_ivSize = 16;
	
	protected static $_iv = 'P8cXUrNfSPc9g+Se';
	protected static $_key = 'shEz+jVxdU1HaHtGKFjTehRjE7akHOI0';	
	
	public static $_seed = 'vlccvcjdal';
	
	public static function encrypt($message) {
		return urlencode(base64_encode(
			mcrypt_encrypt(MCRYPT_TWOFISH, Lib_MCrypt::$_key, $message, MCRYPT_MODE_CBC, Lib_MCrypt::$_iv)));
	}
	
	public static function decrypt($message) {
		return mcrypt_decrypt(MCRYPT_TWOFISH, Lib_MCrypt::$_key, base64_decode(urldecode($message)), MCRYPT_MODE_CBC, Lib_MCrypt::$_iv);
	}
}