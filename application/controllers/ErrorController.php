<?php

require_once 'FacebookController.php';

class ErrorController extends FacebookController
{
	public function init() {
		parent::init();
		$this->requireLogin();
	}
	
    public function errorAction()
    {
        $errors = $this->_getParam('error_handler');
        
        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
        
                // 404 error -- controller or action not found
                $this->getResponse()->setHttpResponseCode(404);
                $this->view->message = 'Page not found';
                break;
            default:
            	// 404 error -- page not found
            	if ($errors->exception instanceof Exception_PageNotFound) {
            		$this->getResponse()->setHttpResponseCode(404);
                	$this->view->message = 'Ceci sera notre page personnalis&eacute; pour les erreur 404';
               		break;
            	}
            	
            	if ($errors->exception instanceof Exception_NoPrivileges) {
            		$this->getResponse()->setHttpResponseCode(200);
                	$this->view->message = 'Si t\'es pas VIP t\'es un looser';
                	$this->render('denied');
               		break;
            	}
            	
            	if ($errors->exception instanceof Exception_NotSubscribed) {
            		$this->getResponse()->setHttpResponseCode(200);
                	$this->view->message = 'Si t\'es pas VIP t\'es un looser';
                	$this->render('inscription');
               		break;
            	}
            	
            	// 400 error -- pas assez de points
        		if ($errors->exception instanceof Exception_NotEnoughPoints) {
            		$this->getResponse()->setHttpResponseCode(200);
                	//$this->view->message = 'Ceci sera notre page personnalis&eacute; pour les erreur 404';
               		$this->render('credits');
            		break;
            	}
            	
                // application error
                $this->getResponse()->setHttpResponseCode(500);
                $this->view->message = 'Application error';
                break;
        }
        
        $this->view->exception = $errors->exception;
        $this->view->request   = $errors->request;
    }

    public function deniedAction() {
    	$this->view->title = "Accès non authorisé";
    }
    
    public function creditsAction() {
    }

}

