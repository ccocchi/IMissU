<?php

//require_once 'Zend/Controller/Action.php';
require_once 'FacebookController.php';

class IndexController extends FacebookController
{
	protected $_model;
	protected $_self;
	
	public function init() {
		parent::init();
		$this->requireLogin();
		$this->_model = new Model_DbTable_Contest();
		$userTable = new Model_DbTable_User();
		$this->_self = $userTable->enableCache($this->fbUserId)->findByFbId($this->fbUserId);
		$this->view->errorMessages = $this->_helper->FlashMessenger->getMessages('error');
	}

	public function preDispatch()
	{
		if (!$this->_helper->acl->isAllowed('index')) {
			throw new Exception_NotSubscribed();
		}
	}


    public function indexAction()
    {
		// pour tous : afficher le concours vote + concours du moment
    	// if non inscrit
		// texte explicatif + lien s'inscire
    	// elsif vip
    	// bonjour machin + viens voir qui a voter pour toi
    	// else
    	// bonjour machin + deviens VIP
    	
    	// var : - list concours ($contests) + 3 premiers de chaque ($participants)
    	//  	 - boolean  is_inscrit
    	//		 - boolean  is_vip
    	//		 - user : info du user
    	
    	$modelContest = new Model_DbTable_Contest();
		$contests = $modelContest->findCurrentContests();
		$participants = array();
		$modelUser = new Model_DbTable_User();

		//$dedicaceTable = new Model_DbTable_Dedicace();
		//$dedicaces = $dedicaceTable->selectGetCurDedicace();
		//$dedicace = $dedicaces[rand(0, $dedicaces->count() - 1)];
		
		foreach ($contests as $contest) {
			$participants[$contest->contest_id] = $modelUser->findTopThreeForContest($contest->contest_id);
		}
		
		$pers = $modelUser->findAccueilPhotos();

		$max = min($pers->count () - 1, 299);
		$persres = array();
		for ( $i = 0; $i < 12; $i++ ) {
			$rd = rand(0, $max);
			$p = $pers[$rd];
			$persres[$i] = $p;
		}       
		
		$next = $modelUser->findRandomsForUser();
		$this->view->userAccueil = $persres;
		$this->view->nextPhotoUser = $next[0];
		$this->view->contests = $contests;
		$this->view->participants = $participants;
		
    	$isInscrit = true;
    	$isVip = false;
    	if (count ($this->_self) == 0)
    		$isInscrit = false;
    	else {
    		if ($this->_helper->acl->getRole() == 'vip')
    			$isVip = true;
    	}
    	
    	//$this->view->dedicace = $dedicace;
    	$this->view->contests = $contests;
    	$this->view->participants = $participants;
    	$this->view->user = $this->_self;
    	$this->view->isInscrit = $isInscrit;
    	$this->view->isVip = $isVip;

    }
    
    
    
	public function testAction()
	{
		$this->view->title = "Test";
		
		//    	$message = $this->_self->user_id . 'cokiRocks' . 2 . time();
//		echo 'Message = ' . $message . '<br />';   	
//		$code = Lib_MCrypt::encrypt($message);
//		echo 'Code = ' . $code . '<br />';
//		//$code64 = base64_encode($code);
//		//echo 'Code64 = ' . $code64 . '<br />';
//		//$decode = base64_decode($code64);
//		//echo 'Decode = ' . $decode . '<br />';
//		$message = Lib_MCrypt::decrypt($code);
//		echo 'Message = ' . $message . '<br />';
//		die;

		var_dump(Lib_Namer::pictureName($this->fbUserId, 1, 'jpg', 'small'));
		die;
    	
    	$cle_taille = mcrypt_module_get_algo_key_size(MCRYPT_3DES);
		// On calcule la taille du vecteur d'initialisation pour l'algo triple des et pour le mode NOFB
		$iv_taille = mcrypt_get_iv_size(MCRYPT_3DES, MCRYPT_MODE_NOFB);
		//On fabrique le vecteur d'initialisation, la constante MCRYPT_RAND permet d'initialiser un vecteur aléatoire
		$iv = mcrypt_create_iv($iv_taille, MCRYPT_RAND);
		
		$cle ="Ceci est une clé censé crypter un message mais à mon avis elle est beaucoup trop longue";
		// On retaille la clé pour qu'elle ne soit pas trop longue
		$cle = substr($cle, 0, $cle_taille);
		
		// Le message à crypter
		$message = "Voici mon super message que je dois crypter";
		// On le crypte
		$message_crypte = mcrypt_encrypt(MCRYPT_3DES, $cle, $message, MCRYPT_MODE_NOFB, $iv);
		// On le décrypte
		$message_decrypte = mcrypt_decrypt(MCRYPT_3DES, $cle, $message_crypte, MCRYPT_MODE_NOFB, $iv);
		
        if ($this->_helper->acl->isAllowed('contest', 'all')) {
            var_dump('allowed');
            }
           
            var_dump($this->_helper->acl->getRole());
            
            $flashmessenger = $this->_helper->getHelper('FlashMessenger');
            $this->view->title = "Index";
            $users = new Model_DbTable_User();
            $this->view->users = $users->fetchAll();
            $u = $users->enableCache()->findByFbId($this->fbUserId);
            //$id = $users->enableCache()->execProc('create_thread', array('julien kikoo', 2, 1));
            //var_dump($id['create_thread']);
            try {
            	$c = $u->findDependentRowset('Model_DbTable_Dedicace');
            //$d = $users->findDedicace($u->user_id);
            //var_dump($d);
            } catch (Exception $e) {
            	$e->getMessage();
            }
            
            $form = new Form_Comment();
            $form->setAction('/imissu/public/user/Coki');
            $this->view->form = $form;
            
            $this->view->dedicace = $c;
            $this->view->name = $u->nickname;
            $this->view->fbUserId = $this->fbUserId;
	}


}



