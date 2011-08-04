<?php
defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));
defined('LIBRARY_PATH') || define('LIBRARY_PATH', realpath(dirname(__FILE__) . '/../library'));
defined('ZEND_PATH') || define('ZEND_PATH', realpath(dirname(__FILE__) . '/../framework'));
defined('PUBLIC_PATH') || define('PUBLIC_PATH', realpath(dirname(__FILE__)));
defined('APPLICATION_ENV') || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'development'));
defined('HOST') || define('HOST', 'apps.facebook.com/imissu_dev/');
defined('USER_IMAGE_DIR_PATH') || define('USER_IMAGE_DIR_PATH', 'images/users/');
defined('USER_THUMB_DIR_PATH') || define('USER_THUMB_DIR_PATH', 'images/users/thumbs/');
defined('CONTEST_IMAGE_DIR_PATH') || define('CONTEST_IMAGE_DIR_PATH', 'images/contests/');
defined('CONTEST_THUMB_DIR_PATH') || define('CONTEST_THUMB_DIR_PATH', 'images/contests/thumbs/');

set_include_path(implode(PATH_SEPARATOR, array(realpath(ZEND_PATH), get_include_path())));

// Load des constantes
require_once APPLICATION_PATH . '/configs/constants.php';

// On a besoin de Zend Application pour lancer notre application
require_once 'Zend/Application.php';

// On lance la session
//require_once 'Zend/Session.php';
//Zend_Session::start();

$application = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini');
$application->bootstrap()->run();
