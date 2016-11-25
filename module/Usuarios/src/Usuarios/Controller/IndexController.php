<?php

namespace Usuarios\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Form\Form;
use Zend\Authentication\AuthenticationService;
use Usuarios\Model\Entity\Usuario;
use Usuarios\Model\Dao\UsuarioDao;
use Usuarios\Form\Usuario\UsuarioEdit;
use Usuarios\Form\Usuario\UsuarioAdd;
use Usuarios\Form\Usuario\UsuarioValidator;
use Usuarios\MisClases\Auth;

class IndexController extends AbstractActionController {

    private $tableGateway;
    private $usuarioDao;
    private $auth;
    private $adapter;

    public function __construct() {
        $this->auth = new AuthenticationService();
    }

    public function setTableGateway($tableGateway) {
        $this->tableGateway = $tableGateway;
    }

    public function setUsuarioDao($usuarioDao) {
        $this->usuarioDao = $usuarioDao;
    }

    public function setAdapter($adapter) {
        $this->adapter = $adapter;
    }

    public function IndexAction() {

        $id = $_SESSION["miSession"]["usuario"]->getId();

        $tipo = $_SESSION["miSession"]["usuario"]->getTipo();
        $dashboard = $this->usuarioDao->getDashboard();
        return new ViewModel(array(
            "dashboard" => $dashboard
        ));
    }

    public function mlLoginAction(){
        $loginMlUrl = $this->params()->fromRoute("param1");
        if($loginMlUrl){
            $loginMlUrl = "https://auth.mercadolibre.com.uy/".$loginMlUrl;
        }

        header("Location:".$loginMlUrl);
        die();

        /*$loginMlUrl = '<a href="' . $loginMlUrl . '">Login using MercadoLibre oAuth 2.0</a>';
        die($loginMlUrl);*/
    }

	public function mlAuthAction(){
		$config = $this->getServiceLocator()->get('config');
		$mlAuth = new Auth($config["ml"]);
		
		$code = null;

		if(isset($_GET["code"])){
			$code = $_GET["code"];
			$respMl = $mlAuth->autenticarMl($code);
			return $this->redirect()->toRoute('usuarios', array(
                        'controller' => 'index',
                        'action' => 'index'
            ));
		}

        
		
	}


}

