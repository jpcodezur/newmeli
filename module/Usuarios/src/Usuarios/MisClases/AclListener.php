<?php

namespace Usuarios\MisClases;

use Zend\Mvc\MvcEvent;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Role\GenericRole as Role;
use Zend\Permissions\Acl\Resource\GenericResource as Resource;

class AclListener implements ListenerAggregateInterface {

    protected $listener = array();
    public $acl;
    public $role;

    public function attach(EventManagerInterface $events) {
        $this->listener[] = $events->attach(MvcEvent::EVENT_DISPATCH, array($this,
            'onDispatch'), 100);
    }

    public function detach(EventManagerInterface $events) {
        foreach ($this->listener as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listener[$index]);
            }
        }
    }

    public function onDispatch(MvcEvent $e) {

        $acl = new Acl();
        $acl->addRole(new Role('admin_sistema'))
                ->addRole(new Role('admin'))
                ->addRole(new Role('suvervisor'))
                ->addRole(new Role('agente'));

        /**
         * Creando los recursos:
         *  - Modulo catalogo, Controlador index
         *  - Modulo usuarios, Controlador index
         *  - Modulo admin, Controlador index
         *  - Modulo admin, Controlador login
         *  - Modulo admin, Controlador usuario
         * Creando privilegios:
         *  - Permitir invitado para acceder al catalogo controller (index)
         *  - Permitir admin para acceder a  todo
         */
        $acl
                ->addResource(new Resource('application:index'))
                ->addResource(new Resource('login:index'))
                ->addResource(new Resource('login:login'))
                ->addResource(new Resource('login:logout'))
                ->addResource(new Resource('usuarios:index'))
                ->addResource(new Resource('usuarios:alerta'))
                ->addResource(new Resource('usuarios:mensaje'))
                ->addResource(new Resource('usuarios:publicar'))
				->addResource(new Resource('usuarios:importCSV'))
                ->addResource(new Resource('usuarios:categoria'))
                ->addResource(new Resource('usuarios:articulo'))
                ->addResource(new Resource('usuarios:login'))
                ->addResource(new Resource('usuarios:usuario'))
                ->addResource(new Resource('usuarios:pagina'))
                ->addResource(new Resource('usuarios:input'))
                ->addResource(new Resource('usuarios:formulario_edit'))
                ->addResource(new Resource('usuarios:pregunta'))
                ->addResource(new Resource('usuarios:respuesta'))
                ->addResource(new Resource('usuarios:formulario'))
                ->addResource(new Resource('usuarios:popup'))
                ->addResource(new Resource('login:registrarse'))
                ->addResource(new Resource('login:recuperarpass'))
                ->addResource(new Resource('usuarios:settings'))
                ->addResource(new Resource('usuarios:profile'))
            ->addResource(new Resource('usuarios:mensajes'))
            ->addResource(new Resource('usuarios:publicacion'))
            //->addResource(new Resource('usuarios:profile'))
//            ->addResource(new Resource('usuarios:profile'))
                //->allow('agente', 'usuarios:index', array('index'))
                //->allow('admin')
                //->allow('agente')
                ->allow('agente', array(
                    'usuarios:index',
                    'usuarios:formulario',
                    'usuarios:alerta',
                    'usuarios:mensaje',
                    'usuarios:login',
                    'usuarios:respuesta',
                    'login:index',
                    'login:login',
                    'usuarios:popup',
                    'usuarios:usuario',
                    'login:logout'
//                        ), array('index', 'wizard', 'msgs', 'resparent', 'adds', 'add', 'actualizospan', 'popup', 'settings', 'savesettings'))
                ), array(
                    'username','send','msgsenviados','view','modificarusuario',
                    'new','index', 'wizard', 'msgs', 'resparent', 'adds', 'add', 'actualizospan', 'popup', 'settings', 'savesettings', 'profile'))
            ->allow('admin', null)
            ->allow('admin_sistema', null);

                /*->deny('admin', array('usuarios:index', 'usuarios:formulario'), array('list', 'add', 'wizard'))*/;

        $application = $e->getApplication();
        $services = $application->getServiceManager();
        $services->setService('AclService', $acl);

        $matches = $e->getRouteMatch();

        $controllerClass = $matches->getParam('controller');
        $controllerArray = explode("\\", $controllerClass);

        $module = strtolower($controllerArray[0]);
        $controller = strtolower($controllerArray[2]);
        $action = $matches->getParam('action');

        $resourceName = $module . ':' . $controller;

        $role = $this->getRole($services);

        if (!$acl->isAllowed($role, $resourceName, $action)) {
            $matches->setParam('controller', 'Usuarios\Controller\Login');
            //$matches->setParam('action', 'index');
        }

        $this->sendToLayout($e, $acl, $role);
    }

    private function sendToLayout($e, $acl, $role) {
        $viewModel = $e->getApplication()->getMvcEvent()->getViewModel();
        $viewModel->acl = $acl;
        $viewModel->role = $role;
    }

    private function getRole($sm) {

        $auth = $sm->get('Usuarios\Model\Login');

        $role = "agente";
        if ($auth->isLoggedIn()) {

            $usuario = $_SESSION["miSession"]["usuario"];

            switch ($usuario->getTipo()) {
                case 1:
                    $role = "admin_sistema";
                    break;
                case 2:
                    $role = "admin";
                    break;
                case 3:
                    $role = "supervisor";
                    break;
                case 4:
                    $role = "agente";
                    break;

                default:
                    break;
            }
        }

        return $role;
    }

}
