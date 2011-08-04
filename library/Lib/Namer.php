<?php

class Lib_Namer {
	public static $_seed = "vlccvcjdal";
	
	public static function pictureName($fbId, $pictureId, $extension = 'jpg', $prefix = 'original') {
		$hash = md5($fbId . Lib_Namer::$_seed);
		return $prefix . '_' . $hash . '_' . $pictureId . '.' . $extension;
	}
	
	public static function contestPictureName($userId, $extension = 'jpg', $prefix = 'original') {
		$hash = md5($userId . Lib_Namer::$_seed);
		return $prefix . '_' . $hash . '_' . $userId . '.' . $extension;
	}
}