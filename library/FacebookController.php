<?php

$err = error_reporting(E_ERROR);
require_once 'facebook/facebook.php';
error_reporting($err);

class FacebookController extends Zend_Controller_Action {

	private static $apiKey = "API_KEY";
	private static $apiSecret = "SECRET_KEY";
	private $canvasUrl = "CANVAS_URL";

	public $fbUserId;

	/**
	 * Facebook api
	 * @var Facebook
	 */
	protected $facebook;

	public function init()
	{
		$this->facebook = new Facebook(FacebookController::$apiKey, FacebookController::$apiSecret);
		$session_key = md5($this->facebook->api_client->session_key);
		if(!Zend_Session::isStarted())
		{
			Zend_Session::setId($session_key);
			Zend_Session::start();
		}
		parent::init();
	}
	
	protected function requireLogin() 
	{
		$this->fbUserId = $this->facebook->require_login();
	}

	protected function _redirect($url, array $options = array())
	{
		$this->facebook->redirect($this->canvasUrl . $url);
	}
}
