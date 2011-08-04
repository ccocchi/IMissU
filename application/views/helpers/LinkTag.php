<?php

class View_Helper_LinkTag extends Zend_View_Helper_Abstract {
	public function linkTag ($link, array $urlOptions = array(), $name = 'default', $reset = true, $encode = true)
	{
		if ($link == null)
			throw new Zend_Exception('Link is empty');

		$path = $this->view->url($urlOptions, $name, $reset, $encode);
		$array = explode('/', $path);
		$narray = array();
		for($i = 3; $i < count($array); $i++)
			$narray[] = $array[$i];
		$realpath = implode('/', $narray);
		
		
		$res = '<a href="'
			. $this->view->serverUrl($realpath)
			. '" target="_top">'
			. $link
			. '</a>';
		return $res;
	}
}