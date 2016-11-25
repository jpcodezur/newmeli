<?php

namespace Usuarios\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Authentication\AuthenticationService;
use Usuarios\Model\Entity\Usuario;
use Usuarios\Model\Dao\UsuarioDao;
use Usuarios\Form\Usuario\UsuarioEdit;
use Usuarios\Form\Usuario\UsuarioAdd;
use Usuarios\Form\Usuario\UsuarioValidator;
use Usuarios\MisClases\SendEmail;

class UsuarioController extends AbstractActionController {

    private $config;
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
        
    }

    public function profileAction() {
        $id = null;
                
        $mensaje = $this->params()->fromRoute('param1','');
        
        if ($this->request->isGet()) {
            $id = $this->request->getQuery('id');
        }
        $this->usuarioDao = $this->getServiceLocator()->get('UsuarioDao');
        
        $usuario = $this->usuarioDao->getUsuario($id);
        
        if(!$usuario){
            if(isset($_SESSION["miSession"]["usuario"])){
                $usuario = $_SESSION["miSession"]["usuario"]; 
                $usuario = $this->usuarioDao->getUsuario($usuario->getId());
            }
        }

        return new ViewModel(array("usuario" => $usuario,"mensaje"=>$mensaje));
    }

    public function getConfig($config) {
        if (!$this->config) {
            $sm = $this->getServiceLocator();
            $this->config = $config; //$sm->get('ConfigIni');
        }
        return $this->config;
    }

    public function ListAction() {
        $usuario = $this->getServiceLocator()->get('UsuarioDao');
        $result = $this->usuarioDao->obtenerTodos();
        $paginator = $result["paginator"];
        $pn = (int) $this->getEvent()->getRouteMatch()->getParam('id', 1);
        $paginator->setCurrentPageNumber($pn);
        return new ViewModel(array("usuarios" => $result["usuarios"], "paginator" => $paginator));
    }

    public function addAction() {
        $form = new UsuarioAdd("UsuarioAdd", $this->tableGateway, $this->adapter);

        if ($this->request->isPost()) {
            $userValidator = new UsuarioValidator();
            $form->setInputFilter($userValidator);
            $data = $this->request->getPost();

            $form->setData($data);

            if ($form->isValid()) {

                $user = new Usuario();

                $upload = new \Zend\File\Transfer\Transfer();

                $files = $upload->getFileInfo();

                foreach ($files as $file => $info) {
                    $avatar = "";
                    if ($info["tmp_name"] != "") {
                        $avatar = file_get_contents($info["tmp_name"]);
                    }
                }

                $email = $this->getRequest()->getPost('email-user', null);

                if (!$this->noExisteEmail($email)) {
                    return new ViewModel(array(
                        "form" => $form,
                        "save" => false,
                        "error" => array("mensaje" => "E-Mail already exist!.")));
                }

                $nombre = $this->getRequest()->getPost('first-name-user', null);
                $apellido = $this->getRequest()->getPost('last-name-user', null);
                $password = $this->getRequest()->getPost('pass-user', null);
                $tipo = $this->getRequest()->getPost('level-user', null);

                $user->setEmail($email);
                $user->setNombre($nombre);
                $user->setApellido($apellido);
                $user->setClave($password);
                $user->setAvatar($avatar);
                $user->setTipo($tipo);

                if ($this->usuarioDao->guardar($user)) {
                    return new ViewModel(array(
                        "form" => $form,
                        "save" => true));
                } else {
                    return new ViewModel(array(
                        "form" => $form,
                        "save" => false,
                        "error" => array("mensaje" => "Error.")));
                }
            }
        }

        return new ViewModel(array("form" => $form,
            "save" => false));
    }

    public function editAction() {

        if ($this->request->isGet()) {
            $id = $this->request->getQuery('id');

            $usuario = $this->usuarioDao->getUsuario($id);

            $form = new UsuarioEdit("UsuarioEdit", $usuario, $this->adapter, $id);

            return new ViewModel(array("form" => $form, "save" => false, "usuario" => $usuario));
        }

        if ($this->request->isPost()) {
            $id = $this->getRequest()->getPost('id-user', null);
            $usuario = $this->usuarioDao->getUsuario($id);
            $userValidator = new UsuarioValidator();
            $form = new UsuarioEdit("UsuarioEdit", $usuario, $this->adapter, $id);
            $form->setInputFilter($userValidator);
            $data = $this->request->getPost();

            $form->setData($data);

            $valid = $form->isValid();

            if (true/* $valid */) {

                $user = new Usuario();

                $upload = new \Zend\File\Transfer\Transfer();

                $files = $upload->getFileInfo();

                foreach ($files as $file => $info) {
                    $avatar = "";
                    if ($info["tmp_name"] != "") {
                        $avatar = file_get_contents($info["tmp_name"]);
                    }
                }

                $email = $this->getRequest()->getPost('email-user', null);
                $nombre = $this->getRequest()->getPost('first-name-user', null);
                $apellido = $this->getRequest()->getPost('last-name-user', null);
                $password = $this->getRequest()->getPost('pass-user', null);
                $tipo = $this->getRequest()->getPost('level-user', null);

                $user->setId($id);
                $user->setEmail($email);
                $user->setNombre($nombre);
                $user->setApellido($apellido);
                $user->setClave($password);
                $user->setAvatar($avatar);
                $user->setTipo($tipo);

                $result = $this->usuarioDao->update($user);

                if ($result["error"] == "0") {
                    return new ViewModel(array(
                        "form" => $form,
                        "save" => true,
                        "usuario" => $result["usuario"]));
                } else {
                    return new ViewModel(array(
                        "form" => $form,
                        "save" => false,
                        "error" => array("mensaje" => $result["mensaje"])));
                }
            }
        }
    }

    public function validarEmailAction() {
        if ($this->request->isGet()) {
            $email = $this->request->getQuery('email');
        }

        $result = $this->noExisteEmail($email);

        $view = new ViewModel(array("result" => $result));

        $view->setTerminal(true);

        return $view;
    }

    public function noExisteEmail($pEmail) {
        $usuarios = $this->usuarioDao->obtenerTodos();

        $users = $usuarios["usuarios"];

        foreach ($users as $usuario) {
            $usu_email = $usuario->getEmail();
            if ($usu_email == $pEmail) {
                return 0;
            }
        }

        return 1;
    }

    public function deleteAction() {
        if ($this->request->isGet()) {

            $id = (int) $this->getEvent()->getRouteMatch()->getParam('id', null);
            if (!$id) {
                $id = $this->request->getQuery('id');
            }

            if ($id) {
                $result = $this->usuarioDao->delete($id);
            }

            if ($result) {
                return $this->redirect()->toRoute('usuarios', array(
                            'controller' => 'usuario',
                            'action' => 'list',
                            'id' => '1'
                ));
            }
        }
    }

    public function savesettingsAction() {

        $user = $_SESSION["miSession"]["usuario"];
        $this->usuarioDao = $this->getServiceLocator()->get('UsuarioDao');
//      $usuario = $this->usuarioDao->getUserByEmail($user->getEmail());

        $pass = $this->getRequest()->getPost('pass', null);
        $repass = $this->getRequest()->getPost('pass-conf', null);

        if ($pass == "" || $repass == "") {
            $mensaje = "<p style='color:red'>Debe completar ambos campos!!!</p>";
            return new ViewModel(array("mensaje" => $mensaje));
        }
        if ($pass !== $repass) {
            $mensaje = "<p style='color:red'>Las contraseñas no coinciden!!!</p>";
            return new ViewModel(array("mensaje" => $mensaje));
        }
        if (strlen($pass) < 4) {
            $mensaje = "<p style='color:red'>Las contraseña debe tener un mínimo de 4 caracteres!!!</p>";
            return new ViewModel(array("mensaje" => $mensaje));
        }

        $user->setClave(md5($pass));

        $this->usuarioDao->update($user);

        $body = "Su nueva contraseña es: " . $pass . "'";
        $to = $user->getEmail();
        $asunto = "GeoMl - Contraseña cambiada";
        $unEmail = new SendEmail($to, "testcodezur@gmail.com", $asunto);
//      $unEmail->sendEmail($body);

        $mensaje = "<p style='color:green'>Su contraseña ha sido cambiada con éxito.</p>";
        return new ViewModel(array("mensaje" => $mensaje));
    }

    public function settingsAction() {
//        $this->layout()->setTemplate('usuarios/usuario/settings');
        return new ViewModel(array(
            "save" => false,
            "mensaje" => "<p style='color:red'>En settings</p>"));
    }

}
