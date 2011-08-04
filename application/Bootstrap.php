<?php

require_once("../library/facebook/facebook.php");

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	public function run()
	{
		Zend_Registry::set('config', new Zend_Config($this->getOptions()));
		parent::run();
	}
	
	/**
	 * Enregistre notre acc�s � la BDD dans le Zend_Registry
	 * @return unknown_type
	 */
	protected function _initDb()
	{
		$config = new Zend_Config($this->getOptions());
		try
		{
			$db = Zend_Db::factory($config->resources->db);
			$db->getConnection();
		}
		catch (Exception $e)
		{
			exit ($e->getMessage());
		}
		
		Zend_Db_Table_Abstract::setDefaultAdapter($db);
		Zend_Registry::set('db', $db);
		return $db;
	}

	/**
	 * Chargement des classes automatiquement selon les conventions PEAR
	 * @return unknown_type
	 */
	protected function _initAutoload()
	{
		$loader = new Zend_Application_Module_Autoloader(array(
			'namespace' => '',
			'basePath'  => APPLICATION_PATH));
		$loader->addResourceType('helper', 'helpers', 'Helper');
		$loader->addResourceType('config', 'configs', 'Config');
		$loader->addResourceType('exception', 'exceptions', 'Exception');
		
		return $loader;
	}
	
	/**
	 * Ajoute le plugin ACL au frontController
	 */
	protected function _initAcl() {
		//$front = Zend_Controller_Front::getInstance();
       	//$front->registerPlugin(new Plugin_Acl());
       	$helperAcl = new Helper_Acl();
       	Zend_Controller_Action_HelperBroker::addHelper($helperAcl);
	}
	
	protected function _initLogging() {
		$logger = new Zend_Log();
        $optionLevel = (int) $this->_options["logging"]["level"];
        $filter = new Zend_Log_Filter_Priority($optionLevel);
        $logger->addFilter($filter);
        $optionPath = $this->_options["logging"]["filename"];
        $writer = new Zend_Log_Writer_Stream($optionPath);
        $logger->addWriter($writer);
        Zend_Registry::set("logger", $logger);
	}
	
	protected function _initRouter() {
		$router = Zend_Controller_Front::getInstance()->getRouter();
		
		$userRoute = new Zend_Controller_Router_Route('user/profile/:username', array(
			'controller' => 'user',
			'action'	=> 'profile'
		));
		
		$archiveRoute = new Zend_Controller_Router_Route('archive/:year/:month', array(
			'controller' => 'archive',
			'action'	=> 'index'
		), array(
			'year' => '\d+',
			'month' => '\d+'
		));
		
		$archiveDetailRoute = new Zend_Controller_Router_Route('archive/:year/:month/:gender', array(
			'controller' => 'archive',
			'action'	=> 'show'
		), array(
			'year' => '\d+',
			'month' => '\d+'
		));
		
		$voteContestRoute = new Zend_Controller_Router_Route('votecontest/:contest_id/:nickname', array(
			'controller' => 'ajax',
			'action' => 'votecontest'
		), array(
			'contest_id' => '\d+'
		));
		
		$voteRoute = new Zend_Controller_Router_Route('vote/:hash/:value', array(
			'controller' => 'ajax',
			'action' => 'vote',
			'value' => 1
		), array(
			'value' => '[1-3]'
		));
		
		$voteprofileRoute = new Zend_Controller_Router_Route('voteprofile/:hash', array(
			'controller' => 'ajax',
			'action' => 'voteprofile',
		));
				
		$votegeneralRoute = new Zend_Controller_Router_Route('votegeneral/:nickname', array(
			'controller' => 'ajax',
			'action' => 'votegeneral',
		));
		
		$nextRoute = new Zend_Controller_Router_Route('next', array(
			'controller' => 'ajax',
			'action' => 'next'
		));
		
		$flashRoute = new Zend_Controller_Router_Route('flash/:hash', array(
			'controller' => 'ajax',
			'action' => 'flash'));
		
		$flashprofileRoute = new Zend_Controller_Router_Route('flashprofile/:hash', array(
			'controller' => 'ajax',
			'action' => 'flashprofile'));
		
		$favoriteRoute = new Zend_Controller_Router_Route('favorite/:hash', array(
			'controller' => 'ajax',
			'action' => 'favorite'));
		
		$deleteRoute = new Zend_Controller_Router_Route('delete', array(
			'controller' => 'ajax',
			'action' => 'delete'));
		
		$updateRoute = new Zend_Controller_Router_Route('users/:type/:page', array(
			'controller' => 'ajax',
			'action' => 'users'
		), array(
			'page' => '\d+'));
		
		$commentsRoute = new Zend_Controller_Router_Route('comments/:user/:page', array(
			'controller' => 'ajax',
			'action' => 'comments'
		), array(
			'page' => '\d+'));
		
		$setprofileRoute = new Zend_Controller_Router_Route('setprofile', array(
			'controller' => 'ajax',
			'action' => 'setprofile'));
		
		$picturesRoute = new Zend_Controller_Router_Route('/user/:username/photos', array(
			'controller' => 'user',
			'action' => 'photos'));
		
		$photoRoute = new Zend_Controller_Router_Route('/user/:username/photo/:id', array(
			'controller' => 'user',
			'action' => 'photo'
		), array(
			'id' => '\d+'));
					
		$delphotoRoute = new Zend_Controller_Router_Route('delphoto/:id/:nickname', array(
			'controller' => 'user',
			'action' => 'delphoto'
		), array(
			'id' => '\d+'));
								
		$delcommentRoute = new Zend_Controller_Router_Route('delcomment/:id', array(
			'controller' => 'ajax',
			'action' => 'delcomment'
		), array(
			'id' => '\d+'));
			
		
		$router->addRoute('user', $userRoute);
		$router->addRoute('vote', $voteRoute);
		$router->addRoute('next', $nextRoute);
		$router->addRoute('flash', $flashRoute);
		$router->addRoute('flash-profile', $flashprofileRoute);
		$router->addRoute('favorite', $favoriteRoute);
		$router->addRoute('delete', $deleteRoute);
		$router->addRoute('archive', $archiveRoute);
		$router->addRoute('update', $updateRoute);
		$router->addRoute('comments', $commentsRoute);
		$router->addRoute('archive-detail', $archiveDetailRoute);
		$router->addRoute('vote-contest', $voteContestRoute);
		$router->addRoute('photos', $picturesRoute);
		$router->addRoute('photo',$photoRoute);
		$router->addRoute('vote-profile', $voteprofileRoute);
		$router->addRoute('vote-general', $votegeneralRoute);
		$router->addRoute('setprofile', $setprofileRoute);
		$router->addRoute('delphoto', $delphotoRoute);
		$router->addRoute('delcomment', $delcommentRoute);
	}
	
	protected function _initCache() {
		$frontendOptions = array('automatic_serialization' => true);
		$backendOptions = array();
		$cache = Zend_Cache::factory('Core', 'Memcached', $frontendOptions);
		Zend_Registry::set('cache', $cache);
		Zend_Db_Table_Abstract::setDefaultMetadataCache($cache);
		$cleaner = new Helper_CacheCleaner();
		Zend_Controller_Action_HelperBroker::addHelper($cleaner);
		return $cache;
	}
	
	protected function _initSession()
	{
		$session = new Zend_Session_Namespace('imissu', true);
		return $session;
	}

	protected function _initView()
	{
		$view = new Zend_View();
		
		// Server URL Configuration
		$view->getHelper('serverUrl')->setHost(HOST);
		
		// Header configuration
		$view->doctype('XHTML1_STRICT');
		$view->headMeta()->appendHttpEquiv('Content-Type', 'text/html;charset=utf-8');
		$view->headTitle()->setSeparator(' - ');
		$view->headTitle('I Miss U');

		// Ajout des helpers personnalisés
		$view->addHelperPath(APPLICATION_PATH . '/views/helpers', 'View_Helper_');
		
		// Initialisation du vue renderer
		$viewRenderer = new Zend_Controller_Action_Helper_ViewRenderer($view);
		$dedicace = new Helper_Dedicaces();
		Zend_Controller_Action_HelperBroker::addHelper($viewRenderer);
		Zend_Controller_Action_HelperBroker::addHelper($dedicace);
	}
}