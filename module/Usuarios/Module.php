<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonModule for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Usuarios;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\TableGateway;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ControllerProviderInterface;
use Zend\ModuleManager\ModuleManager;
use Zend\Mvc\MvcEvent;
use Usuarios\MisClases\ShowAlertas;
use Usuarios\MisClases\ShowMensajes;
use Usuarios\MisClases\AclListener;
use Usuarios\Model\Dao\ArticuloDao;

class Module implements AutoloaderProviderInterface, ServiceProviderInterface, ConfigProviderInterface, ControllerProviderInterface {

    public function init(ModuleManager $moduleManager) {
        $events = $moduleManager->getEventManager();
        $sharedEvents = $events->getSharedManager();
        $sharedEvents->attach(__NAMESPACE__, 'dispatch', array($this, 'initAuth'), 100);
    }

    /*
      public function initAlerts(MvcEvent $e) {
      $application = $e->getApplication();

      } */

    public function initAuth(MvcEvent $e) {

        $application = $e->getApplication();
        $matches = $e->getRouteMatch();
        $controller = $matches->getParam("controller");
        $action = $matches->getParam("action");

        if ($controller === "Usuarios\Controller\Login" && in_array($action, array('index', 'autenticar', 'registrarse', 'recuperarpass','validarclave'))) {
            return;
        }

        if ($controller === "Usuarios\Controller\Index" && in_array($action, array('mlLogin','mlAuth','mlNotify'))) {
            return;
        }

        $sm = $application->getServiceManager();
        $auth = $sm->get('Usuarios\Model\Login');
        if (!$auth->isLoggedIn()) {
            $controller = $e->getTarget();
            return $controller->redirect()->toRoute('usuarios', array('controller' => 'login'));
        }
		
		$config = $sm->get("config");
		$config = $config["ml"];
        $respMl = $auth->isLoggedInMl($config);
        if(!$respMl["succes"]){
            $controller = $e->getTarget();
            return $controller->redirect()->toRoute('usuarios', array(
                'controller' => 'index',
                'action' => 'mlLogin',
                'param1' => $respMl["callback"]
            ));
        }
    }

    public function getAutoloaderConfig() {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    // if we're in a namespace deeper than one level we need to fix the \ in the path
                    __NAMESPACE__ => __DIR__ . '/src/' . str_replace('\\', '/', __NAMESPACE__),
                ),
            ),
        );
    }

    public function getConfig() {
        return include __DIR__ . '/config/module.config.php';
    }

    public function onBootstrap($e) {
        $eventManager = $e->getApplication()->getEventManager();

        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        $aclListener = new AclListener();
        $aclListener->attach($eventManager);

        $alertas = new ShowAlertas();
        $alertas->attach($eventManager);

        $alertas = new ShowMensajes();
        $alertas->attach($eventManager);
    }

    public function getServiceConfig() {
        return array(
            'factories' => array(
                #Adapter
                'db' => function ($sm) {
                    $config = $sm->get("config");
                    $config = $config["db"];
                    $dbAdapter = new Adapter($config);
                    return $dbAdapter;
                },
				'AuthService' => function($sm) {
					$config = $sm->get('config');

					$auth = new Auth($config);

					return $auth;
				},
                #EntidadDao
                'AlertaDao' => function($sm) {
                    return $this->getEntidadDao($sm, "AlertaTableGateway", "Usuarios\Model\Dao\AlertaDao");
                },
                'MensajeDao' => function($sm) {
                    return $this->getEntidadDao($sm, "MensajeTableGateway", "Usuarios\Model\Dao\MensajeDao");
                },
                'UsuarioDao' => function($sm) {
                    return $this->getEntidadDao($sm, "UsuarioTableGateway", "Usuarios\Model\Dao\UsuarioDao");
                },
                'PaginaDao' => function($sm) {
                    return $this->getEntidadDao($sm, "PaginaTableGateway", "Usuarios\Model\Dao\PaginaDao");
                },
                'InputDao' => function($sm) {
                    return $this->getAdapter($sm);
                },
                'PreguntaDao' => function($sm) {
                    return $this->getEntidadDao($sm, "PreguntaTableGateway", "Usuarios\Model\Dao\PreguntaDao");
                },
                'FormularioDao' => function($sm) {
                    return $this->getAdapter($sm);
                },
                'FormularioEditDao' => function($sm) {
                    return $this->getAdapter($sm);
                },
                'RespuestaDao' => function($sm) {
                    return $this->getAdapter($sm);
                },
                'CategoriaDao' => function($sm) {
                    return $this->getAdapter($sm);
                    return $this->getEntidadDao($sm, "CategoriaTableGateway", "Usuarios\Model\Dao\CategoriaDao");
                },
                'ArticuloDao' => function($sm) {
                    //return $this->getAdapter($sm);
					$config = $sm->get("config");
                    $config = $config["ml"];
                    return $this->getEntidadDao($sm, "ArticuloTableGateway", "Usuarios\Model\Dao\ArticuloDao",$config);
                },
                #TableGAteways
                'PreguntaTableGateway' => function ($sm) {
                    $result = $this->getTableGateway($sm, "Usuarios\Model\Entity\Pregunta");
                    return new TableGateway('preguntas', $result["dbAdapter"], null, $result["resultSetPrototype"]);
                },
                'InputTableGateway' => function ($sm) {
                    $result = $this->getTableGateway($sm, "Usuarios\Model\Entity\Input");
                    return new TableGateway('inputs', $result["dbAdapter"], null, $result["resultSetPrototype"]);
                },
                'PaginaTableGateway' => function ($sm) {
                    $result = $this->getTableGateway($sm, "Usuarios\Model\Entity\Pagina");
                    return new TableGateway('paginas', $result["dbAdapter"], null, $result["resultSetPrototype"]);
                },
                'UsuarioTableGateway' => function ($sm) {
                    $result = $this->getTableGateway($sm, "Usuarios\Model\Entity\Usuario");
                    return new TableGateway('usuarios', $result["dbAdapter"], null, $result["resultSetPrototype"]);
                },
                'AlertaTableGateway' => function ($sm) {
                    $result = $this->getTableGateway($sm, "Usuarios\Model\Entity\Alerta");
                    return new TableGateway('alertas', $result["dbAdapter"], null, $result["resultSetPrototype"]);
                },
                'MensajeTableGateway' => function ($sm) {
                    $result = $this->getTableGateway($sm, "Usuarios\Model\Entity\Mensaje");
                    return new TableGateway('mensajes', $result["dbAdapter"], null, $result["resultSetPrototype"]);
                },
                #Login
                'Usuarios\Model\Login' => function ($sm) {
                    $dbAdapter = $sm->get('db');
                    return new \Usuarios\Model\Login($dbAdapter);
                },
                'CategoriaTableGateway' => function ($sm) {
                    $result = $this->getTableGateway($sm, "Usuarios\Model\Entity\Categoria");
                    return new TableGateway('categorias', $result["dbAdapter"], null, $result["resultSetPrototype"]);
                },
                'ArticuloTableGateway' => function ($sm) {
                    $result = $this->getTableGateway($sm, "Usuarios\Model\Entity\Articulo");
                    return new TableGateway('articulos', $result["dbAdapter"], null, $result["resultSetPrototype"]);
                },
            //Plugins
            ),
        );
    }

    public function getControllerConfig() {
        return array(
            'initializers' => array(
                'Usuarios\Controller\Login' => function ($instance, $sm) {
                    if ($instance instanceof \Usuarios\Controller\LoginController) {
                        $locator = $sm->getServiceLocator();
                        $instance->setLogin($locator->get('Usuarios\Model\Login'));
                        $instance->setUsuarioDao($locator->get('UsuarioDao'));
                    }
                },
                'Usuarios\Controller\Index' => function ($instance, $sm) {
                    if ($instance instanceof \Usuarios\Controller\IndexController) {
                        $locator = $sm->getServiceLocator();
                        $instance->setTableGateway($locator->get('UsuarioTableGateway'));
                        $instance->setUsuarioDao($locator->get('UsuarioDao'));
                    }
                },
                'Usuarios\Controller\Usuario' => function ($instance, $sm) {
                    if ($instance instanceof \Usuarios\Controller\UsuarioController) {
                        $locator = $sm->getServiceLocator();
                        $config = $locator->get('ConfigIni');
                        $instance->getConfig($config);
                        $instance->setTableGateway($locator->get('UsuarioTableGateway'));
                        $instance->setUsuarioDao($locator->get('UsuarioDao'));
                        $adapter = $this->getAdapter($locator);
                        $instance->setAdapter($adapter);
                    }
                },
                'Usuarios\Controller\Alerta' => function ($instance, $sm) {
                    if ($instance instanceof \Usuarios\Controller\AlertaController) {
                        $locator = $sm->getServiceLocator();
                        $instance->setAlertaDao($locator->get('AlertaDao'));
                    }
                },
                'Usuarios\Controller\Mensaje' => function ($instance, $sm) {
                    if ($instance instanceof \Usuarios\Controller\MensajeController) {
                        $locator = $sm->getServiceLocator();
                        $instance->setMensajeDao($locator->get('MensajeDao'));
                        $instance->setUsuarioDao($locator->get('UsuarioDao'));
                    }
                },
                'Usuarios\Controller\Pagina' => function ($instance, $sm) {
                    if ($instance instanceof \Usuarios\Controller\PaginaController) {
                        $locator = $sm->getServiceLocator();
                        $instance->setDao($locator->get('PaginaDao'));
                    }
                },
                'Usuarios\Controller\Input' => function ($instance, $sm) {
                    if ($instance instanceof \Usuarios\Controller\InputController) {
                        $locator = $sm->getServiceLocator();
                        $instance->setDao($locator->get('InputDao'));
                        $instance->setPaginaDao($locator->get('PaginaDao'));
                    }
                },
                'Usuarios\Controller\Formulario' => function ($instance, $sm) {
                    if ($instance instanceof \Usuarios\Controller\FormularioController) {
                        $locator = $sm->getServiceLocator();
                        $instance->setPaginaDao($locator->get('PaginaDao'));
                        $instance->setDao($locator->get('FormularioDao'));
                    }
                },
                'Usuarios\Controller\FormularioEdit' => function ($instance, $sm) {
                    if ($instance instanceof \Usuarios\Controller\FormularioEditController) {
                        $locator = $sm->getServiceLocator();
                        $instance->setDao($locator->get('FormularioEditDao'));
                    }
                },
                'Usuarios\Controller\Respuesta' => function ($instance, $sm) {
                    if ($instance instanceof \Usuarios\Controller\RespuestaController) {
                        $locator = $sm->getServiceLocator();
                        $instance->setDao($locator->get('RespuestaDao'));
                    }
                },
                'Usuarios\Controller\Inputs\Input' => function ($instance, $sm) {
                    if ($instance instanceof \Usuarios\Controller\Inputs\InputController) {
                        $locator = $sm->getServiceLocator();
                        $instance->setDao($locator->get('InputDao'));
                    }
                },
                'Usuarios\Controller\Categoria' => function ($instance, $sm) {
                    if ($instance instanceof \Usuarios\Controller\CategoriaController) {
                        $locator = $sm->getServiceLocator();
                        $instance->setDao($locator->get('CategoriaDao'));
                    }
                },
                'Usuarios\Controller\Articulo' => function ($instance, $sm) {
                    if ($instance instanceof \Usuarios\Controller\ArticuloController) {
                        $locator = $sm->getServiceLocator();
                        $instance->setArticuloDao($locator->get('ArticuloDao'));
                    }
                },
            )
        );
    }

    public function getTableGateway($sm, $obj) {

        $dbAdapter = $this->getAdapter($sm);

        $resultSetPrototype = new ResultSet();

        $arr = array();

        $obj = new $obj();

        foreach ($obj as $key => $value) {
            $arr[$key] = $value;
        }

        $data = new \ArrayObject($arr);
        $resultSetPrototype->setArrayObjectPrototype($data);

        return array("dbAdapter" => $dbAdapter, "resultSetPrototype" => $resultSetPrototype);
    }

    public function getAdapter($sm) {
        return $dbAdapter = $sm->get('db');
    }

    public function getEntidadDao($sm, $tableGateway, $entidadDao,$config = null) {
        $tableGateway = $sm->get($tableGateway);
		if($entidadDao == 'Usuarios\Model\Dao\ArticuloDao'){
			$dao = new $entidadDao($tableGateway,null,$config);
		}else{
			$dao = new $entidadDao($tableGateway);
		}
        
        return $dao;
    }

}
