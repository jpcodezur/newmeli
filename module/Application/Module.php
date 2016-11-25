<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Session\Container;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
        $this->initConfig($e);
        $this->initViewRender($e);
        $this->initEnviroment($e);
        $this->initSession();
    }
    
    public function initSession(){
        Container::getDefaultManager()->start();
    }
    
    protected function initConfig($e){
        $application = $e->getApplication();
        $services = $application->getServiceManager();
        $services->setFactory('ConfigIni', function ($services){
            $reader = new \Zend\Config\Reader\Ini();
            $data = $reader->fromFile(__DIR__ . "/config/config.ini");
            return $data;
        });
    }
    
    protected function initViewRender($e){
        $application = $e->getApplication();
        $sm = $application->getServiceManager();
        $viewRender  = $sm->get('ViewManager')->getRenderer();
        $config      = $sm->get('ConfigIni');
        
        $viewRender->headTitle($config['parametros']['titulo']);
        $viewRender->headMeta()
                   ->setCharset($config['parametros']['view']['charset']);
        $viewRender->doctype($config['parametros']['view']['doctype']);
    }
    
    protected function initEnviroment($e){
        error_reporting(E_ALL | E_STRICT);
        ini_set("display_errors",true);
        
        $application = $e->getApplication();
        $config      = $application->getServiceManager()->get('ConfigIni');
        $timeZone = (string)$config['parametros']['timezone'];
        
        if(empty($timeZone)){
            $timeZone = "America/Montevideo";
        }
        date_default_timezone_set($timeZone);
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
}
