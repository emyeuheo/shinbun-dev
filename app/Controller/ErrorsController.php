<?php
App::uses('AppController', 'Controller');
class ErrorsController extends AppController {

/**
 * Controller name
 *
 * @var string
 */
	public $name = 'errors';

/**
 * This controller does not use a model
 *
 * @var array
 */
	public $uses = array();

    function beforeFilter() { 
        $layout = isset($this->params['url']['layout'])?$this->params['url']['layout']:'default';
        if ($layout == 'admin'){
            $session_user = $this->Session->read(SESSION_USER);
            if (empty($session_user) && $this->action != 'login'){
                $layout = 'default';
            }
        }
        $this->layout = $layout;
        $this->view->layout = $layout;
    }

	public function index() {
		$error_code = isset($this->params['url']['error_code'])?$this->params['url']['error_code']:-1;
        
	}
}