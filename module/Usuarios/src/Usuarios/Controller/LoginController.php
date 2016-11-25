<?php

namespace Usuarios\Controller;

if (session_status() == PHP_SESSION_NONE) {
    session_start('teste');
}

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Usuarios\Form\Login\Login;
use Usuarios\Form\Login\LoginValidator;
//use Usuarios\Model\Dao\IUsuarioDao;
use Usuarios\Model\Login as LoginService;
use Zend\Session\Container;
use Usuarios\Model\Entity\Usuario;
use Usuarios\Model\Dao\UsuarioDao;
//xx
use Zend\Authentication\AuthenticationService;
use Zend\Mail;
use Usuarios\MisClases\SendEmail;
use Usuarios\MisClases\Auth;

class LoginController extends AbstractActionController {

    private $config;
    private $tableGateway;
    private $auth;
    private $adapter;
    private $usuarioDao;
    private $login;

    public function __construct() {
        $this->auth = new AuthenticationService();
    }

    public function setLogin(LoginService $login) {
        $this->login = $login;
    }

    public function setUsuarioDao($usuarioDao) {
        $this->usuarioDao = $usuarioDao;
    }

    public function setTableGateway($tableGateway) {
        $this->tableGateway = $tableGateway;
    }

    public function setAdapter($adapter) {
        $this->adapter = $adapter;
    }

    public function getConfig($config) {
        if (!$this->config) {
            $sm = $this->getServiceLocator();
            $this->config = $config; //$sm->get('ConfigIni');
        }
        return $this->config;
    }

    public function indexAction() {

        if ($this->login->isLoggedIn()) {

            return $this->redirect()->toRoute('usuarios', array(
                        'controller' => 'index',
                        'action' => 'index'
            ));
        }

        $form = new Login("login");

        $modeView = new ViewModel(array("form" => $form));

        $this->layout()->setTemplate('usuarios/login/login.phtml');

        return $modeView;
    }

    public function autenticarAction() {

        if (!$this->request->isPost()) {
            $this->redirect()->toRoute('usuarios', array('controller' => 'login'));
        }

        $form = new Login("Login");

        $form->setInputFilter(new LoginValidator());
        $data = $this->request->getPost();

        $form->setData($data);


        if (!$form->isValid()) {
            $modeView = new ViewModel(array("form" => $form));
            $this->layout()->setTemplate('Usuarios/login/login.phtml');
            return $modeView;
        }

        $values = $form->getData();

        $email = $values["nombre"];
        $password = $values["clave"];

        $this->usuarioDao = $this->getServiceLocator()->get('UsuarioDao');

        $this->login->login($email, $password);

        if ($this->login->isLoggedIn()) {

            $miSession = new Container('miSession');

            $usuario = $this->usuarioDao->obtenerCuenta($email, $password);

            if (!isset($miSession->usuario))
                $miSession->usuario = $usuario;

            $this->layout()->mensaje = "LoginCorrecto!!!";
            $this->layout()->colorMensaje = "green";
			$sm = $this->getServiceLocator();
			
			$config = $sm->get("config");
			
            $mlAuth = new Auth($config["ml"]);
            $respMl = $mlAuth->autenticarMl();

            if($respMl["success"]){
                return $this->redirect()->toRoute('usuarios', array(
                            'controller' => 'index',
                            'action' => 'index'
                ));
            }else{
                return $this->redirect()->toRoute('usuarios', array(
                    'controller' => 'index',
                    'action' => 'mlLogin',
                    'param1' => $respMl['callback']
                ));
            }
        } else {
//            $mensaje = "<p style='color:red'>Datos incorrectos.</p>";
//                    $this->layout()->setTemplate('usuarios/login/login.phtml');
//                    return new ViewModel(array("mensaje" => $mensaje));

            return $this->redirect()->toRoute('usuarios', array(
                        'controller' => 'login',
                        'action' => 'index'
            ));
        }
    }

    public function logoutAction() {
        $this->login->logout();
        return $this->redirect()->toRoute('usuarios', array('controller' => 'login', 'action' => 'index'));
    }

    public function registrarseAction() {

        if ($this->request->isPost()) {

            $user = new Usuario();
            $email = $this->getRequest()->getPost('email-user', null);
            $re_email = $this->getRequest()->getPost('reemail-user', null);
            $nombre = $this->getRequest()->getPost('first-name-user', null);
            $apellido = $this->getRequest()->getPost('last-name-user', null);
            $password = $this->getRequest()->getPost('pass-user', null);
            $re_password = $this->getRequest()->getPost('pass_conf', null);
            $tipo = $this->getRequest()->getPost('level-user', null);

            if (isset($email, $re_email)) {
                if ($email !== $re_email) {
                    $mensaje = "<p style='color:red'>Error: La dirección de correo debe coincidir en ambos campos.</p>";
                    $this->layout()->setTemplate('usuarios/login/login.phtml');
                    return new ViewModel(array("mensaje" => $mensaje));
                }
            }

            if (isset($password, $re_password)) {
                if ($password !== $re_password) {
                    $mensaje = "<p style='color:red'>Error: La contraseña debe coincidir en ambos campos.</p>";
                    $this->layout()->setTemplate('usuarios/login/login.phtml');
                    return new ViewModel(array("mensaje" => $mensaje));
                }
            }

            if (!$this->noExisteEmail($email)) {
                $this->layout()->setTemplate('usuarios/login/login.phtml');
                return new ViewModel(array(
                    "save" => false,
                    "mensaje" => "<p style='color:red'>Ya existe un usuario con ese email.</p>"));
            }

            $user->setEmail($email);
            $user->setNombre($nombre);
            $user->setApellido($apellido);
            $user->setClave($password);
            $user->setTipo($tipo);
            $user->setEstado(0);
            $user->setClaveActivacion(md5($email));

            if ($this->usuarioDao->guardar($user)) {

                //Envio de link de activacion
//                $query = "UPDATE usuarios SET estado = 1 WHERE email ='" . $_POST["email-user"] . "'";
//                $current_id = $this->usuarioDao->update($user);
//                
//                if (!empty($current_id)) {
//                    $actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]" . "activar.php?email=" . $user->getEmail();
//                    $toEmail = $_POST["userEmail"];
//                    $subject = "User Registration Activation Email";
//                    $content = "Clickear en el link para activar tu cuenta. <a href='" . $actual_link . "'>" . $actual_link . "</a>";
//                    $mailHeaders = "From: Admin\r\n";
//
//                    $unEmail = new SendEmail($toEmail, "atfede@gmail.com", $subject);
//                    $unEmail->sendEmail($body);
//                    if (mail($toEmail, $subject, $content, $mailHeaders)) {
//                        $message = "You have registered and the activation mail is sent to your email. Click the activation link to activate you account.";
//                    }
//                    unset($_POST);
//                }
                //Envio de link prueba

                $body = "click en el link para activar";
                $body .= "<a href='http://64.22.74.53/tutesisenlinea/public/usuarios/login/validarclave?clave=" . $user->getClaveActivacion() . "'>Activar</a>";
                $to = $user->getEmail();
                $asunto = "GeoMl - Activar cuenta";

                $unEmail = new SendEmail($to, "testcodezur@gmail.com", $asunto);
                $unEmail->sendEmail($body);

                $mensaje = "<p style='color:green'>Usuario creado satisfactoriamente. Verifique su email para activar su cuenta.</p>";

                $this->layout()->setTemplate('usuarios/login/login.phtml');
                return new ViewModel(array("mensaje" => $mensaje));
            } else {
                $mensaje = "<p style='color:red'>Error creando la cuenta</p>";

                $this->layout()->setTemplate('usuarios/login/login.phtml');
                return new ViewModel(array("mensaje" => $mensaje));
            }
        }

//      $this->layout()->setTemplate('usuarios/login/success.phtml');
        $this->layout()->setTemplate('usuarios/login/login.phtml');
//        return new ViewModel(array("mensaje" => "Hola")); //crear nuevo View
    }

    public function noExisteEmail($pEmail) {
//        $usuario = $this->getServiceLocator()->get('UsuarioDao');
        $this->usuarioDao = $this->getServiceLocator()->get('UsuarioDao');
        $usuarios = $this->usuarioDao->obtenerTodos("all");

        $users = $usuarios["usuarios"];

        foreach ($users as $usuario) {
            $usu_email = $usuario->getEmail();
            if ($usu_email == $pEmail) {
                return 0;
            }
        }
        return 1;
    }

    public function validarclaveAction() {
        $activar = false;

        $clave = $this->getRequest()->getQuery("clave", null);
        if ($clave) {
            $activar = $this->usuarioDao->validarClave($clave);
        }

        $mensaje = "";

        if ($activar) {
            $mensaje = "<h2 style='color:green'>Usuario activado con exito</h2>";
        }

        return $this->redirect()->toRoute('usuarios', array(
                    'controller' => 'index',
                    'action' => 'index',
                    'mensaje' => $mensaje
        ));
    }

    public function recuperarpassAction() {

        $user = new Usuario();
        $email = $this->getRequest()->getPost('email-user', null);

        if ($email == "") {
            $mensaje = "<p style='color:red'></p>";
            $this->layout()->setTemplate('usuarios/login/recuperarpass.phtml');
            return new ViewModel(array("mensaje" => $mensaje));
        }

        $this->usuarioDao = $this->getServiceLocator()->get('UsuarioDao');
        $usuario = $this->usuarioDao->getUserByEmail($email);

        $randomPass = md5(date("Y-m-h h:m:s"));
        $randomPass = substr($randomPass, 0, 6);

        $user->setId($usuario->getId());
        $user->setEmail($usuario->getEmail());
        $user->setNombre($usuario->getNombre());
        $user->setApellido($usuario->getApellido());
        $user->setTipo($usuario->getTipo());
        $user->setEstado($usuario->getEstado());
        $user->setClave($randomPass);

        $body = "Su nueva contraseña es: " . $randomPass . "'";
        $to = $email;
        $asunto = "GeoMl - Recuperar contraseña";

        $unEmail = new SendEmail($to, "testcodezur@gmail.com", $asunto);
        $unEmail->sendEmail($body);

        $this->usuarioDao->updatePassword($user);

        $mensaje = "<p style='color:green'>Verifique su email para recuperar su contraseña.</p>";

        $this->layout()->setTemplate('usuarios/login/recuperarpass.phtml');
        return new ViewModel(array("mensaje" => $mensaje));
    }

    public function modificarusuarioAction() {
        $user = new Usuario();
        $nom = $_POST["nom"];
        $apellido = $_POST["apellido"];
        $email = $_POST["email"];

        $pass = $_POST["password"];
        $repass = $_POST["repassword"];

        $usuario = null;

        if (isset($_SESSION["miSession"]["usuario"])) {
            $usuario = $_SESSION["miSession"]["usuario"];
        }

        if ($usuario) {

            $user->setId($usuario->getId());
            $user->setEmail($email);
            $user->setNombre($nom);
            $user->setApellido($apellido);

            $user->setTipo($usuario->getTipo());
            $user->setEstado($usuario->getEstado());

            $mensaje = "Usuario modiciado satisfactoriamente";

            $status = true;

            if ($pass && strlen($pass) < 4) {
                $mensaje = "Error: La password debe ser de almenos 4 caracteres";
            } else {
                $this->updateUser($_REQUEST);
            }


            if ($status) {
                $upd = $this->usuarioDao->update($user);
                if(!$upd){
                    $mensaje = "Error al actualizar";
                }
            } else {
                $mensaje = "Error al actualizar";
            }
        } else {
            $mensaje = "Error al actualizar";
        }

        return $this->redirect()->toRoute('usuarios', array(
                    'controller' => 'usuario',
                    'action' => 'profile',
                    'param1' => $mensaje
        ));
    }

    public function updateUser($params){
        $user = new Usuario();
        $user->setEmail($params["email"]);
        $user->setNombre($params["nom"]);
        $user->setApellido($params["apellido"]);

        if($params["password"] && ($params["password"] == $params["repassword"])){
            $user->setClave(($params["password"]));
            $result = $this->usuarioDao->updatePassword($user);
        }
        $result = $this->usuarioDao->update($user);

    }

}
